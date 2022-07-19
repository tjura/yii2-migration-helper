<?php

namespace tjura\migration\src;

use tjura\migration\enums\MigrationEnum;
use tjura\migration\traits\QuestionHelperTrait;
use yii\helpers\Console;

use function array_merge;
use function implode;
use function in_array;
use function sprintf;

class ConsoleMigrationBuilder
{
    use QuestionHelperTrait;

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
        if (MigrationEnum::FK_SUFFIX->value === $lastThreeCharacters) {
            return true;
        }

        return false;
    }

    protected function createForeignKey(?string $tableName): string
    {
        if (null === $tableName) {
            $tableName = Console::prompt(text: 'Foreign Key table name: ', options: ['required' => true]);
        }

        return sprintf('foreignKey(%s)', $tableName);
    }

    /**
     * @param string $columnName
     * @return string
     * @todo encapsulate console question to proper class, builder should focus only on building migrate query
     */
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