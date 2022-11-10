<?php

namespace tjura\migration\src;

use tjura\migration\model\Column;
use yii\helpers\Console;

use function array_pop;
use function implode;
use function in_array;

/**
 * This decorator trait should contain only console asking logic
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class QuestionHelper
{
    public function askAboutColumnName(): string
    {
        return $this->ask(question: 'COLUMN NAME:');
    }

    public function askAboutTableName(string $question = 'TABLE NAME:'): string
    {
        return $this->ask(question: $question);
    }

    public function ask(string $question, bool $required = true): string
    {
        return Console::prompt(text: $question, options: ['required' => $required]);
    }

    public function askAboutForeignKey(string $columnName): ?string
    {
        $fkTable = CommandBuilder::getTableNameFromColumnName(columnName: $columnName);
        $askResult = Console::select(prompt: 'Do you want create FK with "' . $fkTable . '" table?', options: [
            'yes' => 'yes',
            'no' => 'no',
            'custom' => 'customize table name',
        ]);

        return match ($askResult) {
            'yes' => $fkTable,
            'no' => null,
            'custom' => Console::prompt(text: 'Foreign Key table name: ', options: ['required' => true]),
        };
    }

    protected function askAboutColumnTypes(array &$types): void
    {
        Console::output();
        Console::output(string: 'Help: integer,string,boolean,defaultValue(1),notNull');
        Console::output(string: 'Live blank to finish or type "undo" to remove last');
        Console::output();
        $this->showSelectedColumnTypes(types: $types);

        while ($type = Console::prompt(text: 'Type:')) {
            if ('undo' === $type) {
                array_pop(array: $types);
            } else {
                $types[] = $type;
            }
            $this->showSelectedColumnTypes(types: $types);
        }
    }

    public function showSelectedColumnTypes(array $types): void
    {
        if ($types) {
            Console::stdout('Current types:');
            Console::output(implode(',', $types));
        }
    }

    /**
     * @return Column[]
     */
    public function askAboutColumns(string $message = 'Do you want create column'): array
    {
        $results = [];

        while (Console::confirm(message: $message, default: true)) {
            $results[] = $this->askAboutColumn();
        }

        return $results;
    }

    public function askAboutColumn(): Column
    {
        $columnName = $this->askAboutColumnName();
        $fk = null;
        $types = [];

        if (CommandBuilder::isColumnNameContainsRelation(columnName: $columnName)) {
            $fk = $this->askAboutForeignKey(columnName: $columnName);
            $types = ['integer'];
        }

        $this->askAboutColumnTypes(types: $types);

        if (null === $fk && in_array('integer', $types)) {
            $fk = $this->askAboutForeignKey(columnName: $columnName);
        }

        return new Column($columnName, $types, $fk);
    }

}