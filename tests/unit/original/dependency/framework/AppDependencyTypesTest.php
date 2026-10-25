<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Framework\AppDependencyTypes;

it('ensures the file exists', function() {
    (object) $appDependencyTypes = new AppDependencyTypes;

    expect($appDependencyTypes->source())->toBeReadableFile();
});
