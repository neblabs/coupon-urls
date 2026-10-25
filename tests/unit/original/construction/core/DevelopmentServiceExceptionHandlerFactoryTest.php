<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Core\DevelopmentServiceExceptionHandlerFactory;
use CouponURLs\Original\Construction\Core\ProductionServiceExceptionHandlerFactory;
use CouponURLs\Original\Core\Exceptions\Handlers\SilentServiceExceptionHandler;
use CouponURLs\Original\Core\Exceptions\Handlers\UnhandledServiceExceptionHandler;
use CouponURLs\Original\Environment\Abilities\Environment;

it('can create when environment is development', function() {
    /** @var \Mockery\MockInterface&Environment */
    (object) $environemt = Mockery::mock(Environment::class);

    (object) $developmentServiceExceptionHandlerFactory = new DevelopmentServiceExceptionHandlerFactory;

    $environemt->shouldReceive('isDevelopment')->once()->andReturn(true);

    expect($developmentServiceExceptionHandlerFactory->canCreate($environemt))->toBeTrue();
});

it('can create when environment is testing', function() {
    /** @var \Mockery\MockInterface&Environment */
    (object) $environemt = Mockery::mock(Environment::class);

    (object) $developmentServiceExceptionHandlerFactory = new DevelopmentServiceExceptionHandlerFactory;

    $environemt->shouldReceive('isDevelopment')->once()->andReturn(false);
    $environemt->shouldReceive('isTesting')->once()->andReturn(true);


    expect($developmentServiceExceptionHandlerFactory->canCreate($environemt))->toBeTrue();
});

it('cannot create when environment is production', function() {
    /** @var \Mockery\MockInterface&Environment */
    (object) $environemt = Mockery::mock(Environment::class);

    (object) $developmentServiceExceptionHandlerFactory = new DevelopmentServiceExceptionHandlerFactory;

    $environemt->shouldReceive('isDevelopment')->once()->andReturn(false);
    $environemt->shouldReceive('isTesting')->once()->andReturn(false);

    expect($developmentServiceExceptionHandlerFactory->canCreate($environemt))->toBeFalse();
});

it('creates a silent exception handler', function() {
    (object) $developmentServiceExceptionHandlerFactory = new DevelopmentServiceExceptionHandlerFactory;

    expect($developmentServiceExceptionHandlerFactory->create())
        ->toBeInstanceOf(UnhandledServiceExceptionHandler::class);
});