<?php

namespace tjura\migration\traits;

use tjura\migration\enums\MigrationEnum;
use yii\helpers\Console;

use function array_pop;
use function implode;
use function str_replace;

trait QuestionHelperTrait
{
    protected function askAboutColumnName(): string
    {
        return $this->ask(question: 'COLUMN NAME:');
    }

    protected function askAboutTableName(string $question = 'TABLE NAME:'): string
    {
        return $this->ask(question: $question);
    }

    protected function ask(string $question, bool $required = true): string
    {
        return Console::prompt(text: $question, options: ['required' => $required]);
    }

    protected function askAboutForeignKey(string $columnName): string
    {
        $fkTable = $this->getTableNameFromFk(columnName: $columnName);
        $askResult = Console::select(prompt: 'Do you want create FK with ' . $fkTable . ' table?', options: [
            'Y' => 'Yes',
            'N' => 'No',
            'C' => 'Customize table name',
        ]);

        return match ($askResult) {
            'Y' => $this->createForeignKey(tableName: $fkTable),
            'N' => '',
            'C' => $this->createForeignKey(tableName: null),
        };
    }

    private function getTableNameFromFk(string $columnName): string
    {
        return str_replace(search: MigrationEnum::FK_SUFFIX->value, replace: '', subject: $columnName);
    }

    protected function askAboutTypes(array &$types): void
    {
        Console::output();
        Console::output(string: 'Help: integer,string,boolean,defaultValue(1),notNull');
        Console::output(string: 'Live blank to finish or type "undo" to remove last');
        Console::output();
        $this->showSelectedTypes(types: $types);

        while ($type = Console::prompt(text: 'Type:')) {
            if ('undo' === $type) {
                array_pop(array: $types);
            } else {
                $types[] = $type;
            }
            $this->showSelectedTypes(types: $types);
        }
    }

    protected function showSelectedTypes(array $types): void
    {
        if ($types) {
            Console::stdout('Current types:');
            Console::output(implode(',', $types));
        }
    }

}