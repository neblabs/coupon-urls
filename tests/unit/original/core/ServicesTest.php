<?php declare(strict_types=1);

use CouponURLs\Original\Core\Abilities\Service;
use CouponURLs\Original\Core\Abilities\ServicesContainer;
use CouponURLs\Original\Core\Services;

it('adds and gets services', function() {
    /** @var \Mockery\MockInterface&Service */
    (object) $service1 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service2 = Mockery::mock(Service::class);

    $service1->shouldReceive('id')->atLeast()->once()->andReturn('service1ID');
    $service2->shouldReceive('id')->atLeast()->once()->andReturn('service2ID');

    (object) $services = new Services;

    $services->add($service1);
    $services->add($service2);

    //gets
    expect($services->get('service1ID'))->toBe($service1);
    expect($services->get('service2ID'))->toBe($service2);

    //has
    expect($services->has('service1ID'))->toBeTrue();
    expect($services->has('service2ID'))->toBeTrue();
});

it('removes service', function() {
    /** @var \Mockery\MockInterface&Service */
    (object) $firstService = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $secondService = Mockery::mock(Service::class);

    $firstService->shouldReceive('id')->andReturn('firstServiceID');
    $secondService->shouldReceive('id')->andReturn('secondServiceID');

    (object) $services = new Services;

    $services->add($firstService);
    $services->add($secondService);

    $services->remove($firstService);

    // throws exception because it no longer exists
    expect($services->has('firstServiceID'))->toBeFalse();
    expect(fn() => expect($services->get('firstServiceID')))->toThrow(Error::class);

    // this shouldnt have been removed so no exception is thrown
    expect($services->has('secondServiceID'))->toBeTrue();
    expect($services->get('secondServiceID'))->toBe($secondService);

    $services->remove($secondService);

    //now an exception shall be thrown
    expect($services->has('secondServiceID'))->toBeFalse();
    expect(fn() => expect($services->get('secondServiceID')))->toThrow(Error::class);
});

it('starts services, removes each queued service after starting', function() {
    /** @var \Mockery\MockInterface&ServicesContainer */
    (object) $servicesContainer = Mockery::mock(ServicesContainer::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $service1 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service2 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service3 = Mockery::mock(Service::class);

    $service1->shouldReceive('id')->andReturn('service1ID');
    $service2->shouldReceive('id')->andReturn('service2ID');
    $service3->shouldReceive('id')->andReturn('service3ID');

    (object) $services = new Services;

    $services->add($service1);
    $services->add($service2);
    $services->add($service3);

    $service1->shouldReceive('start')->once()->with($servicesContainer);
    $service2->shouldReceive('start')->once()->with($servicesContainer);
    $service3->shouldReceive('start')->once()->with($servicesContainer);

    $servicesContainer->shouldReceive('onServiceStart')->once()->with($service1);
    $servicesContainer->shouldReceive('onServiceStart')->once()->with($service2);
    $servicesContainer->shouldReceive('onServiceStart')->once()->with($service3);

    $services->start($servicesContainer);
});

it('stops the services, adds each service back to queued after stoping, SERVICES ARE STOPPED FROM LAST TO FIRST, THIS IS VERY IMPORTANT!', function() {
    /** @var \Mockery\MockInterface&ServicesContainer */
    (object) $servicesContainer = Mockery::mock(ServicesContainer::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $service1 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service2 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service3 = Mockery::mock(Service::class);

    $service1->shouldReceive('id')->andReturn('service1ID');
    $service2->shouldReceive('id')->andReturn('service2ID');
    $service3->shouldReceive('id')->andReturn('service3ID');

    (object) $services = new Services;

    $services->add($service1);
    $services->add($service2);
    $services->add($service3);

    $service3->shouldReceive('stop')->once()->with($servicesContainer)->ordered()->globally();
    $service2->shouldReceive('stop')->once()->with($servicesContainer)->ordered()->globally();
    $service1->shouldReceive('stop')->once()->with($servicesContainer)->ordered()->globally();

    //ORDER IS VERY IMPORTANT
    $servicesContainer->shouldReceive('onServiceStop')->once()->with($service3)->ordered()->globally();
    $servicesContainer->shouldReceive('onServiceStop')->once()->with($service2)->ordered()->globally();
    $servicesContainer->shouldReceive('onServiceStop')->once()->with($service1)->ordered()->globally();

    $services->stop($servicesContainer);
});

it('dispatches to ServicesContainer when an exception is thrown when starting a service, execution must continue when the ServicesContainer doesnt throw any further exceptions', function() {
    /** @var \Mockery\MockInterface&ServicesContainer */
    (object) $servicesContainer = Mockery::mock(ServicesContainer::class);

    /** @var \Mockery\MockInterface&Service */
    (object) $service1 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service2 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service3 = Mockery::mock(Service::class);

    $service1->shouldReceive('id')->andReturn('service1ID');
    $service2->shouldReceive('id')->andReturn('service2ID');
    $service3->shouldReceive('id')->andReturn('service3ID');

    (object) $services = new Services;

    $services->add($service1);
    $services->add($service2);
    $services->add($service3);

    $servicesContainer->shouldReceive('onServiceException')->once()->withSomeOfArgs($service2)->andReturn('uyf');

    $service1->shouldReceive('start')->once()->with($servicesContainer);
    $service2->shouldReceive('start')->once()->with($servicesContainer)->andThrow(
        Error::class, message: 'Exception intentionally thrown by the $service2 mock!'
    );
    $service3->shouldReceive('start')->once()->with($servicesContainer);

    $servicesContainer->shouldReceive('onServiceStart')->once()->with($service1);
    $servicesContainer->shouldReceive('onServiceStart')->once()->with($service3);

    //NEVER SHOULD RECEIBE!!
    $servicesContainer->shouldReceive('onServiceStart')->never()->with($service2);

    $services->start($servicesContainer);
});