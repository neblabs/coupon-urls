<?php

use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {

    require_once __DIR__ . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';
    require_once __DIR__ . '/vendor/php-stubs/woocommerce-stubs/woocommerce-stubs.php';

    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_72
    ]);

    $rectorConfig->paths([
        './*',
    ]);

    $rectorConfig->skip([
        './vendor/**/*',
        './tests/**/*',
        './bin/**/*',
        './storage/**/*',
        './coupon-urls.php'
    ]);

};