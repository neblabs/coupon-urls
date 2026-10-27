<?php

return [
    'slug' => 'coupon-urls-for-woocommerce',
    'pluginFile' => 'index.php', # this will be rewritten to the target build by the header-injector when doing the transformations.
    'requires' => [
        'wp' => 5.0,
        'php' => 7.2
    ],
    'urls' => [
        'plugin' => '', #todo, shall be couponsplus.app
        'organization' => '', # todo, shall be neblabs.com
    ],
    'files' => [
        'plugin' => [
            'in' => 'index.php',
            'out' => '((slug)).php'
        ],
        'readme' => 'readme.md'
    ]
];
