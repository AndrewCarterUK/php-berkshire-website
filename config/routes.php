<?php

return [
    [
        'method'  => 'GET',
        'pattern' => '/',
        'service' => 'action.page',
    ],
    [
        'method'  => 'POST',
        'pattern' => '/submit-talk',
        'service' => 'action.submit-talk',
    ],
    [
        'method'  => 'GET',
        'pattern' => '{page:.+}',
        'service' => 'action.page',
    ]
];
