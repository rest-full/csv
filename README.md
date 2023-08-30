# CSV

## About CSV

filesystem plugin for CSV.

## Installation

* Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
* Run `php composer.phar require rest-full/csv` or composer installed globally `compser require rest-full/csv` or composer.json `"rest-full/csv": "1.0.0"` and install or update.

## Usage

The export:
 ```php
 <?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/pathServer.php';

use Restfull\CommaSeparatedValues\CommaSeparatedValues;

try {
    $csv = new CommaSeparatedValues('contacts.csv');
    //the $positions variable can contain the initial and columns keys to tell which line to start those positions.
    $positions = ['initial' => 3, 'columns' => [ 2, 3]];
    //or the $positions variable can't contain the initial and columns keys to tell which line to start those positions.
    $positions = [0, 1];
    var_dump($csv->reading($positions));
} catch (\Restfull\Error\Exceptions $e) {
    echo $e->getMessage();
}
```

the import:
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/pathServer.php';

use Restfull\CommaSeparatedValues\CommaSeparatedValues;

try {
    $csv = new CommaSeparatedValues('contacts.csv');
    var_dump($csv->writing(['casa' => 0, 'mae' => 1]));
} catch (\Restfull\Error\Exceptions $e) {
    echo $e->getMessage();
}
```

## License

The csv is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
 
