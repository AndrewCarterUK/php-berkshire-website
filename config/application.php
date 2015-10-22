<?php

use PHPBerks\Application;

$container = include 'config/container.php';
return new Application($container);
