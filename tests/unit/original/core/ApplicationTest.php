<?php declare(strict_types=1);

use CouponURLs\Original\Core\Abilities\HandleableServiceException;
use CouponURLs\Original\Core\Abilities\Service;
use CouponURLs\Original\Core\Application;
use CouponURLs\Original\Core\Services;
use CouponURLs\Original\Dependency\ContainableDependencies;
use CouponURLs\Original\Events\Registrator\Abilities\RegistrableSubscribers;
use CouponURLs\Original\Events\Subscribers;
use CouponURLs\Original\Events\Wordpress\Abilities\ExceptionHandler;
use CouponURLs\Original\Events\Wordpress\Hooks;
use CouponURLs\Original\Exceptions\UnhandledExceptionHandler;

it('adds some services', function() {
    /** @var \Mockery\MockInterface&HandleableServiceException */
    (object) $serviceExceptionHandler = Mockery::mock(HandleableServiceException::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $thirdService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->atLeast()->once()->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->atLeast()->once()->andReturn('secondServiceID');
    $thirdService->shouldReceive('id')->atLeast()->once()->andReturn('thirdServiceID');

    (object) $application = new Application($serviceExceptionHandler);    

    $application->addService($firstService);
    $application->addService($secondService);
    $application->addService($thirdService);


    expect($application->queuedServices()->count())->toBe(3);
    expect($application->queuedServices()->get('firstServiceID'))->toBe($firstService);
    expect($application->queuedServices()->get('secondServiceID'))->toBe($secondService);
    expect($application->queuedServices()->get('thirdServiceID'))->toBe($thirdService);

    expect($application->runningServices()->count())->toBe(0);
});

it('starts the services, none throw an exception so we"re good.', function() {
    /** @var \Mockery\MockInterface&HandleableServiceException */
    (object) $serviceExceptionHandler = Mockery::mock(HandleableServiceException::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $thirdService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->atLeast()->once()->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->atLeast()->once()->andReturn('secondServiceID');
    $thirdService->shouldReceive('id')->atLeast()->once()->andReturn('thirdServiceID');

    (object) $application = new Application($serviceExceptionHandler);

    $application->addService($firstService);
    $application->addService($secondService);
    $application->addService($thirdService);

    $firstService->shouldReceive('start')->once()->with($application);
    $secondService->shouldReceive('start')->once()->with($application);
    $thirdService->shouldReceive('start')->once()->with($application);

    $application->start();

    expect($application->queuedServices()->count())->toBe(0);
    expect($application->runningServices()->count())->toBe(3);

    expect($application->runningServices()->has('firstServiceID'))->toBeTrue();
    expect($application->runningServices()->has('secondServiceID'))->toBeTrue();
    expect($application->runningServices()->has('thirdServiceID'))->toBeTrue();
});

it('starts the services, one throws an exception that is handled by the exception handler, the rest of the services SHOULD continue executing when the exception handler doesnt throw any further exceptions.', function() {
    /** @var \Mockery\MockInterface&HandleableServiceException */
    (object) $serviceExceptionHandler = Mockery::mock(HandleableServiceException::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $thirdService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->zeroOrMoreTimes()->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->zeroOrMoreTimes()->andReturn('secondServiceID');
    $thirdService->shouldReceive('id')->zeroOrMoreTimes()->andReturn('thirdServiceID');

    (object) $application = new Application($serviceExceptionHandler);

    $application->addService($firstService);
    $application->addService($secondService);
    $application->addService($thirdService);

    $firstService->shouldReceive('start')->once()->with($application);
    $secondService->shouldReceive('start')->once()->with($application)->andThrow(Error::class);
    $thirdService->shouldReceive('start')->once()->with($application);

    $serviceExceptionHandler->shouldReceive('handle')->once()->withArgs(
        fn($exeption, $service) => $exeption instanceof Throwable && $service === $secondService
    );

    $application->start();

    expect($application->queuedServices()->count())->toBe(0);
    expect($application->runningServices()->count())->toBe(2);

    expect($application->runningServices()->has('firstServiceID'))->toBeTrue();
    expect($application->runningServices()->has('secondServiceID'))->toBeFalse();
    expect($application->runningServices()->has('thirdServiceID'))->toBeTrue();
});

it('stops the services, making sure that they have the same order as initially', function() {
    /** @var \Mockery\MockInterface&HandleableServiceException */
    (object) $serviceExceptionHandler = Mockery::mock(HandleableServiceException::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $thirdService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->atLeast()->once()->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->atLeast()->once()->andReturn('secondServiceID');
    $thirdService->shouldReceive('id')->atLeast()->once()->andReturn('thirdServiceID');

    (object) $application = new Application($serviceExceptionHandler);

    $application->addService($firstService);
    $application->addService($secondService);
    $application->addService($thirdService);

    $firstService->shouldReceive('start')->once()->with($application);
    $secondService->shouldReceive('start')->once()->with($application);
    $thirdService->shouldReceive('start')->once()->with($application);

    $application->start();

    expect($application->queuedServices()->count())->toBe(0);
    expect($application->runningServices()->count())->toBe(3);

    expect($application->runningServices()->has('firstServiceID'))->toBeTrue();
    expect($application->runningServices()->has('secondServiceID'))->toBeTrue();
    expect($application->runningServices()->has('thirdServiceID'))->toBeTrue();

    //lets stop now

    $firstService->shouldReceive('stop')->once()->with($application);
    $secondService->shouldReceive('stop')->once()->with($application);
    $thirdService->shouldReceive('stop')->once()->with($application);

    $application->stop();

    expect($application->queuedServices()->count())->toBe(3);
    expect($application->runningServices()->count())->toBe(0);

    (object) $expectedServices = new Services;

    //the order matters!
    $expectedServices->add($firstService);
    $expectedServices->add($secondService);
    $expectedServices->add($thirdService);

    expect($application->queuedServices())->toEqual($expectedServices);
});


it('todo: service register extra services on start, those services should also get started by the Application', function() {
    /** @var \Mockery\MockInterface&HandleableServiceException */
    (object) $serviceExceptionHandler = Mockery::mock(HandleableServiceException::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $thirdService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->atLeast()->once()->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->atLeast()->once()->andReturn('secondServiceID');
    $thirdService->shouldReceive('id')->atLeast()->once()->andReturn('thirdServiceID');

    (object) $application = new Application($serviceExceptionHandler);

    $application->addService($firstService);
    $application->addService($secondService);
    $application->addService($thirdService);

    $firstService->shouldReceive('start')->once()->with($application);
    $secondService->shouldReceive('start')->once()->with($application);
    $thirdService->shouldReceive('start')->once()->with($application)->andReturnUsing(function() {
        exit('rereuned!');
    });

    $application->start();

    expect($application->queuedServices()->count())->toBe(0);
    expect($application->runningServices()->count())->toBe(3);

    expect($application->runningServices()->has('firstServiceID'))->toBeTrue();
    expect($application->runningServices()->has('secondServiceID'))->toBeTrue();
    expect($application->runningServices()->has('thirdServiceID'))->toBeTrue();
})->todo();