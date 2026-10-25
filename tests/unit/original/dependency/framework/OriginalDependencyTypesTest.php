<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Framework\OriginalDependencyTypes;

it('ensures the file exists', function() {
    (object) $originalDependencyTypes = new OriginalDependencyTypes;

    expect($originalDependencyTypes->source())->toBeReadableFile();
});