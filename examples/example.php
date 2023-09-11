<?php

    namespace Paalg\Csv;

    use Paalg\Csv\Csv;

    require '../vendor/autoload.php';

    $csv = new Csv('../data/example.csv', true);
    foreach ($csv as $line) {
        print_r($line);
    }
