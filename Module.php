<?php

namespace tjura\migration;

use tjura\migration\commands\MigrateController;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\console\Application;
use yii\console\Application as ConsoleApplication;

class Module extends BaseModule
{
    public const MODULE_NAME = 'migrationGenerator';
}