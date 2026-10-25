<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\GettableCollection;
use CouponURLs\Original\Core\Abilities\Service;
use CouponURLs\Original\Core\Abilities\ServicesContainer;
use CouponURLs\Original\Core\Services\AppServices;

use function CouponURLs\Original\Utilities\Collection\_;

it('adds the services to the application', function() {
    /** @var \Mockery\MockInterface&GettableCollection */
    (object) $servicesGetter = Mockery::mock(GettableCollection::class);
    /** @var \Mockery\MockInterface&ServicesContainer */
    (object) $servicesContainer = Mockery::mock(ServicesContainer::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service1 = Mockery::mock(Service::class);
    /** @var \Mockery\MockInterface&Service */
    (object) $service2 = Mockery::mock(Service::class);

    (object) $appServices = new AppServices($servicesGetter);

    $servicesGetter->shouldReceive('get')->once()->andReturn(_(
        $service1,
        $service2,
    ));

    $servicesContainer->shouldReceive('addService')->once()->withSomeOfArgs($service1);
    $servicesContainer->shouldReceive('addService')->once()->withSomeOfArgs($service2);

    $appServices->start($servicesContainer);
});