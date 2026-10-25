<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\OverloadableFactory;
use CouponURLs\Original\Construction\FactoryOverloader;
use function CouponURLs\Original\Utilities\Collection\{_, o};

it('picks the first factory that returns true on canCreate()', function() {
    /** @var \Mockery\MockInterface&OverloadableFactory */
    (object) $firstFactory = Mockery::mock(OverloadableFactory::class);
    /** @var \Mockery\MockInterface&OverloadableFactory */
    (object) $secondFactory = Mockery::mock(OverloadableFactory::class);
    /** @var \Mockery\MockInterface&OverloadableFactory */
    (object) $thirdFactory = Mockery::mock(OverloadableFactory::class);
    (object) $value = o(id: 10);
    (object) $factoryOverloader = new FactoryOverloader(_(
        $firstFactory,
        $secondFactory,
        $thirdFactory
   )); 

   $firstFactory->shouldReceive('canCreate')->once()->with($value)->andReturn(false);
   $secondFactory->shouldReceive('canCreate')->once()->with($value)->andReturn(true);
   $thirdFactory->shouldReceive('canCreate')->never()->with($value)->andReturn(false);

   expect($factoryOverloader->overload($value))->toBe($secondFactory);
});