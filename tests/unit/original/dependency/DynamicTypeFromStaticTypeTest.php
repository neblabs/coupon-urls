<?php declare(strict_types=1);

use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\DynamicTypeFromStaticType;

it('the type() is the resolved static type from the static class', function() {
    /** @var \Mockery\MockInterface&StaticType */
    $staticType = \Mockery::mock(StaticType::class);

    $staticType->shouldReceive('type')->andReturn('the statically typed mock type!');

    (object) $dynamicTypeFromAStaticType = new DynamicTypeFromStaticType($staticType::class);


    expect($dynamicTypeFromAStaticType->type())->toBe('the statically typed mock type!');
});

it('the defaultType() is the given static class', function() {
    /** @var \Mockery\MockInterface&StaticType */
    $staticType = \Mockery::mock(StaticType::class);

    $staticType->shouldReceive('type')->never();

    (object) $dynamicTypeFromAStaticType = new DynamicTypeFromStaticType($staticType::class);


    expect($dynamicTypeFromAStaticType->defaultType())->toBe($staticType::class);
});
