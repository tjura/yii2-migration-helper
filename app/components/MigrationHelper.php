<?php

namespace tjura\components;

/**
 * Class MigrationHelper
 * @package tjura\components\controllers
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */
class MigrationHelper
{
    /**
     * @todo THIS IS OLD but working CODE change it to Yii console command
     */
    public static function run(){
        $availableCommands = [];
        $availableCommands[1] = 'Create table';
        $availableCommands[2] = 'Add column';
        $availableCommands[3] = 'Drop Column';
        $availableCommands[4] = 'Add Junction Table';
        $availableCommands[6] = 'Redo last';
        $availableCommands[7] = 'Down last';
        $availableCommands[8] = 'Up';
        $availableCommands[9] = 'BLANK';

        echo PHP_EOL;
        foreach ($availableCommands as $i => $name) {
            echo $i . '. ' . $name . PHP_EOL;
        }
        echo PHP_EOL;

        $selectedOperation = readline('Number: ');

        if (empty($selectedOperation)) {
            return;
        }

        $commands = [];
        switch ($selectedOperation) {
            case 1:
                $tbName = readline("new table name: ");
                $fieldName = null;
                $fields = [];

                while ("" !== $fieldName) {
                    $fieldName = readline("field name: ");
                    if ($field = newField($fieldName)) {
                        $fields[] = "$field";
                    }
                }
                $fieldsImplode = implode(',', $fields);
                $commands[] = "migrate/create create_{$tbName}_table --fields=\"{$fieldsImplode}\"";
                break;

            case 2:
                $tbName = readline("table name: ");
                $field = readline("field name: ");
                $fieldsImplode = newField($field);

                $commands[] = "migrate/create add_{$field}_column_to_{$tbName}_table --fields=\"{$fieldsImplode}\"";

                break;
            case 3:
                $tbName = readline("table name: ");
                $field = readline("field name: ");
                $fieldsImplode = newField($field, false);

                $commands[] = "migrate/create drop_{$field}_column_from_{$tbName}_table --fields=\"{$fieldsImplode}\"";

                break;
            case 4:
                $tbName = readline("table 1 name: ");
                $tb2Name = readline("table 2 name: ");

                $commands[] = "migrate/create create_junction_table_for_{$tbName}_and_{$tb2Name}_tables";

                break;
            case 6:
                $commands[] = "migrate/redo";
                break;
            case 7:
                $commands[] = "migrate/down";
                break;
            case 8:
                $commands[] = "migrate/up";
                break;
            case 9:
                $migrationName = readline("migration name: ");
                $commands[] = "migrate/create {$migrationName}";
                break;
        }

        foreach ($commands as $command) {
            echo $command;
            echo shell_exec("php yii {$command}  --interactive=0");
        }

        if (!in_array($selectedOperation, [8, 7, 6])) {
            $runMigrations = readline("Migration up? 1/0 ");
            if ($runMigrations) {
                echo shell_exec("php yii migrate/up  --interactive=0");
            }
        }

        function newField(string $field, bool $askForFk = true): ?string
        {
            $fk = null;
            $createFk = null;
            $type = null;

            if ($askForFk) {
                if (false !== strpos($field, '_id')) {
                    $createFk = readline("create fk from ID 0/1: ");
                    if ($createFk) {
                        $type = 'integer';
                        $fk = str_replace('_id', '', $field);
                    }
                }
            }

            if (null === $type) {
                $type = readline('type: ');
            }

            if ($askForFk && null === $fk) {
                $fk = readline('fk: ');
            }

            if ($field) {
                $line = "{$field}:{$type}";
                if ($fk) {
                    $line .= ":foreignKey({$fk})";
                }

                return "$line";
            }

            return null;
        }
    }

}