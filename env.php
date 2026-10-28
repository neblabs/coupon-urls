<?php

return [
    'slug' => 'coupon-urls-for-woocommerce',
    'pluginFile' => 'index.php', # this will be rewritten to the target build by the header-injector when doing the transformations.
    'requires' => [
        'wp' => 5.1,
        'php' => 7.2
    ],
    'urls' => [
        'plugin' => '',
        'organization' => '',
    ],
    'files' => [
        'plugin' => [
            'in' => 'index.php',
            'out' => 'coupon-urls.php' # DO NOT change it to coupon-urls-for-woocommerce.php since this is what users have installed and renaming it would make all 100s of sites being uninstalled
        ],
        'readme' => 'readme.md'
    ],
    'runtimes' => [
        'php' => [
            'build'       => '8.2',
            'lint_source' => '8.4',
            'lint_build'  => '8.4', # this is is post edp-build which means we should lint to php 72 but this linting also includes vendor so for now let's just link 84. but this is a todo.
            'tests_e2e'   => 'latest',
            'tests_php'   => '8.2',
        ],
        'node' => [
            'build' => '16',
        ],
    ],
    'tests' => [
        'playwright' => true,
        'php'    => true,
    ],
];
