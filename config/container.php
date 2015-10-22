<?php

use PHPBerks\Container\Container;
use PHPBerks\Container\ContainerService;

return new Container([
    'services' => [
        'routes' => include 'config/routes.php',
    ],
    'classes' => [
        'plates' => [
            'class'     => 'League\Plates\Engine',
            'arguments' => [ 'templates' ],
        ],
        'content-loader' => [
            'class'     => 'PHPBerks\Content\ContentLoader',
            'arguments' => [ 'content' ],
        ],
        'action.page' => [
            'class'     => 'PHPBerks\Action\PageAction',
            'arguments' => [
                new ContainerService('plates'),
                new ContainerService('content-loader'),
            ],
        ],
        'action.submit-talk' => [
            'class' => 'PHPBerks\Action\SubmitTalkAction',
        ],
    ],
]);
