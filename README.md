## Yii 2 - Migration generator

<p style="text-align:center;">
 <a title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.2-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=for-the-badge"></a>  
 <a title="PHP Versions Supported"><img alt="" src="https://img.shields.io/badge/Framework-Yii2-777bb3.svg?logo=framework&logoColor=white&labelColor=555555&style=for-the-badge"></a>
</p>

### Description && How its works

This is an interactive Yii migrate command expansion. It will help you to create basic migrations.
This extension basically creating commands according Yii documentation
https://www.yiiframework.com/doc/guide/2.0/en/db-migrations

Example output:

```bash
yii migrate/create create_post_table --fields="title:string,body:text,author_id:integer:notNull:foreignKey(user)"
```

### Usage

```bash
php yii migrate
```

### Available options

1. Create table
2. Drop table
3. Add column
4. Drop Column
5. Add Junction Table
6. Redo last
7. Down last
8. Create empty migration
9. Up

### Installation instructions

```php
composer require tjura/yii2-migration-helper:dev-develop --dev
```

Update your console.php config file
```php
'controllerMap' => [
        'migrate' => [
            'class' => \tjura\migration\commands\MigrateController::class,
        ]
]
```

### What's next in version before release 1.0

- Improve code quality and write tests