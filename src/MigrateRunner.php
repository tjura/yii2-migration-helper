<?php

namespace tjura\migration\src;

use tjura\migration\commands\MigrateController;
use tjura\migration\enums\AvailableCommandsEnum;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Allows run all extension logic without Yii2 instance
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class MigrateRunner
{
    protected QuestionHelper $questionHelper;

    public function __construct(protected MigrateController $migrateController)
    {
        $this->questionHelper = new QuestionHelper();
    }

    /**
     * @throws Exception
     */
    public function menu(): int
    {
        $options = AvailableCommandsEnum::getLabels();

        foreach ($options as $key => $value) {
            Console::output(string: " $key - $value");
        }

        Console::output(string: ' CTRL+C - for exit in any moment');

        return match (Console::select(prompt: 'Select action:', options: $options)) {
            AvailableCommandsEnum::CREATE_TABLE->value => $this->createTable(),
            AvailableCommandsEnum::DROP_TABLE->value => $this->dropTable(),
            AvailableCommandsEnum::ADD_COLUMN->value => $this->addColumn(),
            AvailableCommandsEnum::DROP_COLUMN->value => $this->dropColumn(),
            AvailableCommandsEnum::ADD_JUNCTION_TABLE->value => $this->addJunctionTable(),
            AvailableCommandsEnum::REDO_LAST->value => $this->migrateController->actionRedo(),
            AvailableCommandsEnum::DOWN_LAST->value => $this->migrateController->actionDown(),
            AvailableCommandsEnum::UP->value => $this->migrateController->actionUp(),
            AvailableCommandsEnum::CREATE_EMPTY_MIGRATION_FILE->value => $this->migrateController->actionCreate(
                $this->questionHelper->ask(question: 'Migration name: '),
            ),
        };
    }

    protected function createBuilder(): CommandBuilder
    {
        return new CommandBuilder(tableName: $this->questionHelper->askAboutTableName());
    }

    public function addJunctionTable(): int
    {
        $builder = $this->createBuilder();
        $builder->buildAddJunctionTableCommand(
            secondTableName: $this->questionHelper->askAboutTableName(question: 'SECOND TABLE NAME:')
        );

        return $this->execute(builder: $builder);
    }

    public function addColumn(): int
    {
        $builder = $this->createBuilder();
        $builder->buildAddColumnCommand(column: $this->questionHelper->askAboutColumn());

        return $this->execute(builder: $builder);
    }

    public function dropColumn(): int
    {
        Console::output(string: 'Warning! This migration do not support down option');
        $builder = $this->createBuilder();
        $builder->buildDropColumnCommand(columnName: $this->questionHelper->askAboutColumnName());

        return $this->execute(builder: $builder);
    }

    public function dropTable()
    {
        Console::output(string: 'Warning! This migration do not support down option');
        $builder = $this->createBuilder();
        $builder->buildDropTableCommand();

        return $this->execute(builder: $builder);
    }

    public function createTable(): int
    {
        $builder = $this->createBuilder();
        $builder->setColumns(columns: $this->questionHelper->askAboutColumns());
        $builder->buildCreateTableCommand();

        return $this->execute(builder: $builder);
    }

    protected function execute(CommandBuilder $builder): int
    {
        $builder->populateColumns($this->migrateController);
        Console::output(string: $builder->getFinalCommand());
        $result = $this->migrateController->actionCreate(name: $builder->getCommand());

        if (ExitCode::OK !== $result) {
            return $result;
        }

        return $this->migrateController->actionUp();
    }

}