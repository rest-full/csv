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