<?php

namespace tjura\migration\commands;

use tjura\migration\src\MigrateRunner;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * This class wrap logic of that extensions and pass it to Yii Migrate Controller
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class MigrateController extends BaseMigrateController
{
    public $defaultAction = 'menu';

    protected MigrateRunner $runner;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->runner = new MigrateRunner($this);
    }

    /**
     * @throws Exception
     */
    public function actionMenu(): int
    {
        return $this->runner->menu();
    }

    /**
     * @param $name
     * @return int
     * @throws Exception
     */
    public function actionCreate($name): int
    {
        parent::actionCreate(name: $name);

        return ExitCode::OK;
    }

    /**
     * Creating Junction Table between two tables
     */
    public function actionAddJunctionTable(): int
    {
        return $this->runner->addJunctionTable();
    }

    /**
     * Add column interactive
     */
    public function actionAddColumn(): int
    {
        return $this->runner->addColumn();
    }

    /**
     * Create interactive dropping column migration
     */
    public function actionDropColumn(): int
    {
        return $this->runner->dropColumn();
    }

    /**
     * Drop table - interactive - down option is not supported for this migration
     */
    public function actionDropTable(): int
    {
        return $this->runner->dropTable();
    }

    /**
     * Create new table - interactive
     */
    public function actionCreateTable(): int
    {
        return $this->runner->createTable();
    }

    protected function getNewMigrations()
    {
        $this->migrationTestMode();

        return parent::getNewMigrations();
    }

    protected function getMigrationNameLimit()
    {
        $this->migrationTestMode();

        return parent::getMigrationNameLimit();
    }

    /**
     * Disable real migration Yii functions when you are in test mode (injected void controller)
     * @return void
     */
    private function migrationTestMode()
    {
        if ('test' === $this->id) {
            Console::output('Migration controller is in test mode - exiting');
            exit(ExitCode::OK);
        }
    }

}
