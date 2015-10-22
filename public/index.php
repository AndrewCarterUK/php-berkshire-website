<?php

chdir(__DIR__.'/../');
require_once 'vendor/autoload.php';

$application = include 'config/application.php';
$application->run();
