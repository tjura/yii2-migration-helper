<?php

namespace tjura\migration\commands;

use tjura\migration\enums\AvailableCommandsEnum;
use tjura\migration\src\ConsoleMigrationBuilder;
use tjura\migration\traits\QuestionHelperTrait;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;

use function implode;

/**
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class MigrateController extends BaseMigrateController
{
    use QuestionHelperTrait;

    public $defaultAction = 'menu';

    protected ConsoleMigrationBuilder $generator;

    public function init()
    {
        $this->generator = new ConsoleMigrationBuilder();
        parent::init();
    }

    /**
     * Interactive menu
     * @throws Exception
     */
    public function actionMenu(): int
    {
        $options = AvailableCommandsEnum::getLabels();

        foreach ($options as $key => $value) {
            Console::output(string: " $key - $value");
        }

        Console::output(string: ' CTRL+C - for exit in any moment');

        return match (Console::select(prompt: 'Select action:', options: $options)) {
            AvailableCommandsEnum::CREATE_TABLE->value => $this->actionCreateTable(),
            AvailableCommandsEnum::DROP_TABLE->value => $this->actionDropTable(),
            AvailableCommandsEnum::ADD_COLUMN->value => $this->actionAddColumn(),
            AvailableCommandsEnum::DROP_COLUMN->value => $this->actionDropColumn(),
            AvailableCommandsEnum::ADD_JUNCTION_TABLE->value => $this->actionAddJunctionTable(),
            AvailableCommandsEnum::REDO_LAST->value => $this->actionRedo(),
            AvailableCommandsEnum::DOWN_LAST->value => $this->actionDown(),
            AvailableCommandsEnum::UP->value => $this->actionUp(),
            AvailableCommandsEnum::CREATE_EMPTY_MIGRATION_FILE->value => $this->actionCreate(
                $this->ask(question: 'Migration name: ')
            ),
        };
    }

    public function actionCreate($name)
    {
        parent::actionCreate(name: $name);

        return ExitCode::OK;
    }

    /**
     * Creating Junction Table between two tabled
     * @throws Exception
     */
    public function actionAddJunctionTable(): int
    {
        $tableName = $this->askAboutTableName();
        $secondTableName = $this->askAboutTableName(question: 'SECOND TABLE NAME:');
        $command = $this->generator->buildAddJunctionTableCommand(
            tableName: $tableName,
            secondTableName: $secondTableName
        );

        return $this->execute(command: $command, actionCallable: function ($command) {
            return $this->actionCreate(name: $command);
        });
    }

    /**
     * Add column interactive
     * @throws Exception
     */
    public function actionAddColumn(): int
    {
        $tableName = $this->askAboutTableName();
        $columnName = $this->askAboutColumnName();
        $field = $this->generator->createColumn(columnName: $columnName);
        $command = $this->generator->buildAddColumnCommand(tableName: $tableName, columnName: $columnName);
        $this->fields[] = $field;

        return $this->execute(command: $command, actionCallable: function ($command) {
            return $this->actionCreate(name: $command);
        });
    }

    /**
     * Create interactive dropping column migration
     * @return int
     * @throws Exception
     */
    public function actionDropColumn(): int
    {
        $tableName = $this->askAboutTableName();
        $columnName = $this->askAboutColumnName();
        $field = $this->generator->createColumn(columnName: $columnName);
        $command = $this->generator->buildDropColumnCommand(tableName: $tableName, columnName: $columnName);
        $this->fields[] = $field;

        return $this->execute(command: $command, actionCallable: function ($command) {
            return $this->actionCreate(name: $command);
        });
    }

    /**
     * Drop table - interactive - down option is not supported for this migration
     * @throws Exception
     */
    public function actionDropTable(): int
    {
        Console::output('Take care - this migration do not support down option');
        $tableName = $this->askAboutTableName();
        $command = $this->generator->buildDropTableCommand(tableName: $tableName);

        return $this->execute(command: $command, actionCallable: function ($command) {
            return $this->actionCreate(name: $command);
        });
    }

    /**
     * Create new table - interactive
     * @throws Exception
     */
    public function actionCreateTable(): int
    {
        $tableName = $this->askAboutTableName();
        $fields = [];
        while (Console::confirm(message: 'Do you want create column', default: true)) {
            $fields[] = $this->generator->createColumn(columnName: $this->askAboutColumnName());
        }
        $command = $this->generator->buildCreateTableCommand(tableName: $tableName);
        $this->fields = $fields;

        return $this->execute(command: $command, actionCallable: function ($command) {
            return $this->actionCreate(name: $command);
        });
    }

    protected function execute(string $command, callable $actionCallable): int
    {
        $fields = '';
        if ($this->fields) {
            $fields = ' --fields="' . implode(',', $this->fields) . '"';
        }
        Console::output(string: 'migrate/create ' . $command . $fields);

        $result = $actionCallable($command);

        if (ExitCode::OK !== $result) {
            return $result;
        }

        return $this->actionUp();
    }
}
