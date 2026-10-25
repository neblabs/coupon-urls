<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependentDynamicTypeFactory;
use CouponURLs\Original\Dependency\Dependency;

it('can only create when its not a Dependency', function() {
    (object) $dependentDynamicTypeFactory = new DependentDynamicTypeFactory;

    expect($dependentDynamicTypeFactory->canCreate(stdClass::class))->toBeTrue();
    expect($dependentDynamicTypeFactory->canCreate(Dependency::class))->toBeFalse();
});
