<?php

namespace tjura\migration;

use tjura\migration\commands\MigrateController;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    
    public function bootstrap(Application $app)
    {
        if ($app->hasModule(Module::MODULE_NAME) && ($module = $app->getModule(Module::MODULE_NAME)) instanceof Module) {
            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = self::getCommandsNamespace();
            }
        }
    }

    private static function getCommandsNamespace(): string
    {
        $reflactionClass = new \ReflectionClass(MigrateController::class);

        return $reflactionClass->getNamespaceName();
    }

}
