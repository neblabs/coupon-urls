<?php

return [
    'slug' => 'coupon-urls-for-woocommerce',
    'pluginFile' => 'index.php', # this will be rewritten to the target build by the header-injector when doing the transformations.
    'requires' => [
        'wp' => 5.1,
        'php' => 7.2
    ],
    'urls' => [
        'plugin' => '', #todo, shall be couponsplus.app
        'organization' => '', # todo, shall be neblabs.com
    ],
    'files' => [
        'plugin' => [
            'in' => 'index.php',
            'out' => 'coupon-urls.php' # DO NOT change it to coupon-urls-for-woocommerce.php since this is what users have installed and renaming it would make all 100s of sites being uninstalled
        ],
        'readme' => 'readme.md'
    ]
];
