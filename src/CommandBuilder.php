<?php

namespace tjura\migration\src;

use tjura\migration\enums\MigrationEnum;
use tjura\migration\model\Column;
use yii\console\controllers\MigrateController;

use function array_merge;
use function implode;
use function sprintf;
use function str_replace;

/**
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class CommandBuilder
{
    /** @var Column[] */
    protected array $columns = [];
    protected readonly string $command;

    public function __construct(protected string $tableName)
    {
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     * @return CommandBuilder
     */
    public function setColumns(array $columns): CommandBuilder
    {
        $this->columns = $columns;

        return $this;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return CommandBuilder
     */
    public function setTableName(string $tableName): CommandBuilder
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function buildCreateTableCommand(): self
    {
        $this->command = sprintf('create_%s_table', $this->getTableName());

        return $this;
    }

    public function buildDropTableCommand(): self
    {
        $this->command = sprintf('drop_%s_table', $this->tableName);

        return $this;
    }

    public function buildAddColumnCommand(Column $column): self
    {
        $this->command = sprintf('add_%s_column_to_%s_table', $column->getName(), $this->tableName);
        $this->setColumns([$column]);

        return $this;
    }

    public function buildDropColumnCommand(string $columnName): self
    {
        $this->command = sprintf('drop_%s_column_from_%s_table', $columnName, $this->tableName);

        return $this;
    }

    public function buildAddJunctionTableCommand(string $secondTableName): self
    {
        $this->command = sprintf(
            'create_junction_table_for_%s_column_from_%s_table',
            $this->tableName,
            $secondTableName
        );

        return $this;
    }

    public function buildForeignKey(?string $tableName): string
    {
        return sprintf('foreignKey(%s)', $tableName);
    }

    /**
     * This only used for tests / validate script
     */
    public function getFinalCommand(): string
    {
        return sprintf('migrate/create %s %s', $this->command, $this->buildFieldsString());
    }

    /**
     * Build column string that can be interpreted by --field parameter
     * @param Column $column
     * @return string
     */
    protected function buildColumnString(Column $column): string
    {
        $fk = $column->getFk();
        $ar = array_merge([$column->getName()], $column->getTypes(), $fk ? [$this->buildForeignKey($fk)] : []);

        return implode(':', $ar);
    }

    public function populateColumns(MigrateController $migrateController): void
    {
        $migrateController->fields = $this->getValueForFieldsParameter();
    }

    protected function getValueForFieldsParameter(): array
    {
        $result = [];
        foreach ($this->getColumns() as $column) {
            $result[] = $this->buildColumnString(column: $column);
        }

        return $result;
    }

    protected function buildFieldsString(): string
    {
        if ($fields = $this->getValueForFieldsParameter()) {
            return '--fields="' . implode(separator: ',', array: $fields) . '"';
        }

        return '';
    }

    public static function isColumnNameContainsRelation(string $columnName): bool
    {
        $lastThreeCharacters = substr(string: $columnName, offset: -3);
        if (MigrationEnum::FK_SUFFIX->value === $lastThreeCharacters) {
            return true;
        }

        return false;
    }

    public static function getTableNameFromColumnName(string $columnName): string
    {
        return str_replace(search: MigrationEnum::FK_SUFFIX->value, replace: '', subject: $columnName);
    }

}