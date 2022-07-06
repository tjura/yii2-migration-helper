<?php

namespace tjura\migration\src;

use yii\helpers\Console;

use function array_merge;
use function array_pop;
use function implode;
use function in_array;
use function sprintf;
use function str_replace;

class ConsoleMigrationBuilder
{
    protected const FK_SUFFIX = '_id';

    public function buildCreateTableCommand(string $tableName): string
    {
        return sprintf('create_%s_table', $tableName);
    }

    public function buildDropTableCommand(string $tableName): string
    {
        return sprintf('drop_%s_table', $tableName);
    }

    public function buildAddColumnCommand(string $tableName, string $columnName): string
    {
        return sprintf('add_%s_column_to_%s_table', $columnName, $tableName);
    }

    public function buildDropColumnCommand(string $tableName, string $columnName): string
    {
        return sprintf('drop_%s_column_from_%s_table', $columnName, $tableName);
    }

    public function buildAddJunctionTableCommand(string $tableName, string $secondTableName): string
    {
        return sprintf('create_junction_table_for_%s_column_from_%s_table', $tableName, $secondTableName);
    }

    protected function detectPossibleFk(string $columnName): bool
    {
        $lastThreeCharacters = substr($columnName, -3);
        if (self::FK_SUFFIX === $lastThreeCharacters) {
            return true;
        }

        return false;
    }

    protected function getTableNameFromFk(string $columnName): string
    {
        return str_replace(self::FK_SUFFIX, '', $columnName);
    }

    protected function createForeignKey(?string $tableName): string
    {
        if (null === $tableName) {
            $tableName = Console::prompt(text: 'Foreign Key table name: ', options: ['required' => true]);
        }

        return sprintf('foreignKey(%s)', $tableName);
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

    protected function showSelectedTypes(array $types): void
    {
        if ($types) {
            Console::stdout('Current types:');
            Console::output(implode(',', $types));
        }
    }

    public function askAboutTypes(array &$types): void
    {
        Console::output();
        Console::output('Help: integer,string,boolean,defaultValue(1),notNull');
        Console::output('Live blank to finish or type "undo" to remove last');
        Console::output();
        $this->showSelectedTypes(types: $types);

        while ($type = Console::prompt(text: 'Type:')) {
            if ('undo' === $type) {
                array_pop($types);
            } else {
                $types[] = $type;
            }
            $this->showSelectedTypes(types: $types);
        }
    }

    public function createColumn(string $columnName): string
    {
        $fk = null;
        $types = [];

        if ($this->detectPossibleFk($columnName)) {
            $fk = $this->askAboutForeignKey($columnName);
            $types = ['integer', 'unsigned'];
        }

        $this->askAboutTypes($types);

        if (null === $fk && in_array('integer', $types)) {
            $fk = $this->askAboutForeignKey($columnName);
        }

        $result = array_merge([$columnName], $types, $fk ? [$fk] : []);

        return implode(':', $result);
    }

}