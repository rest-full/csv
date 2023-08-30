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