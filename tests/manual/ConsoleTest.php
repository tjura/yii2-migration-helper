<?php
/**
 * Simple manual test that you can run from CLI using docker or any other local env
 * docker run --rm -v $(pwd):/app -w /app -it php:8.1-fpm php tests/manual/ConsoleTest.php
 * @author Tomasz Jura <jura.tomasz@gmail.com>
 */

namespace tjura\migration\tests\manual;

use tjura\migration\commands\MigrateController;
use tjura\migration\src\MigrateRunner;

require '/app/vendor/autoload.php';

$controller = new MigrateController('test', 'test');
$runner = new MigrateRunner($controller);
$runner->menu();