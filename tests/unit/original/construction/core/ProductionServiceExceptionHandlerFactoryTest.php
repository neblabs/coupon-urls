<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Core\ProductionServiceExceptionHandlerFactory;
use CouponURLs\Original\Core\Exceptions\Handlers\SilentServiceExceptionHandler;
use CouponURLs\Original\Environment\Abilities\Environment;

it('can create only when environment is production', function() {
    /** @var \Mockery\MockInterface&Environment */
    (object) $environemt = Mockery::mock(Environment::class);

    (object) $productionServiceExceptionHandlerFactory = new ProductionServiceExceptionHandlerFactory;

    $environemt->shouldReceive('isProduction')->once()->andReturn(true);

    expect($productionServiceExceptionHandlerFactory->canCreate($environemt))->toBeTrue();
});

it('cannot create when environment is NOT production', function() {
    /** @var \Mockery\MockInterface&Environment */
    (object) $environemt = Mockery::mock(Environment::class);

    (object) $productionServiceExceptionHandlerFactory = new ProductionServiceExceptionHandlerFactory;

    $environemt->shouldReceive('isProduction')->once()->andReturn(false);

    expect($productionServiceExceptionHandlerFactory->canCreate($environemt))->toBeFalse();
});

it('creates a silent exception handler', function() {
    (object) $productionServiceExceptionHandlerFactory = new ProductionServiceExceptionHandlerFactory;

    expect($productionServiceExceptionHandlerFactory->create())
        ->toBeInstanceOf(SilentServiceExceptionHandler::class);
});
