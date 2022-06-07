## Yii 2 - Migration helper

<p style="text-align:center;">
 <a title="PHP Versions Supported"><img alt="PHP Versions Supported" src="https://img.shields.io/badge/php->=7.2-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=for-the-badge"></a>  
 <a title="PHP Versions Supported"><img alt="" src="https://img.shields.io/badge/Framework-Yii2-777bb3.svg?logo=framework&logoColor=white&labelColor=555555&style=for-the-badge"></a>
</p>

### Description && How its works

Is just simple interactive PHP CLI script that creating migrations commands according to Yii2 documentation.
Options 1, 2 and 4 supports creating foreignKey
https://www.yiiframework.com/doc/guide/2.0/en/db-migrations

Example script output:

```bash
yii migrate/create create_post_table --fields="title:string,body:text,author_id:integer:notNull:foreignKey(user)"
```

### Usage

```bash
php ./vendor/tjura/yii2-migration-helper/src/runner.php
```

### Available options

1. Create table
2. Add column
3. Drop Column
4. Add Junction Table
5. Redo last
6. Down last
7. Up
8. BLANK

### Standalone tests

```bash
docker run --rm -v $(pwd):/app -w /app -it php:cli php src/runner.php
```

### What's next in version 1.x

Currently, this is first script - some kind of proof of concept. Its work but will be changed in future
In version 1.x I want to move this script into Yii console commands.