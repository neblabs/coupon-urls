<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\OverloadableFactory;
use CouponURLs\Original\Construction\Dependency\DynamicTypeFactory;
use CouponURLs\Original\Construction\FactoryOverloader;
use CouponURLs\Original\Dependency\Abilities\DynamicType;

use function CouponURLs\Original\Utilities\Collection\_;

it('picks the correct factory from the overloader and uses it to create the DynamicType', function() {
    /** @var \Mockery\MockInterface&OverloadableFactory */
    (object) $factory1 = Mockery::mock(OverloadableFactory::class);
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);

    (object) $dynamicTypeFactory = new DynamicTypeFactory(
        new FactoryOverloader(_(
            $factory1
        ))
    );

    $factory1->shouldReceive('canCreate')->once()->with('dependency type')->andReturn(true);
    $factory1->shouldReceive('create')->once()->with('dependency type')->andReturn($dynamicType);

    expect($dynamicTypeFactory->create('dependency type'))->toBe($dynamicType);
});
