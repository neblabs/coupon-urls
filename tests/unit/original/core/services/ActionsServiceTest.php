<?php declare(strict_types=1);

use CouponURLs\Original\Core\Abilities\ServicesContainer;
use CouponURLs\Original\Core\Services;
use CouponURLs\Original\Core\Services\ActionsService;
use CouponURLs\Original\Core\Services\DependenciesService;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Events\Wordpress\Hooks;

beforeEach(function() {
   /** @var \Mockery\MockInterface&ServicesContainer */
    $this->servicesContainer = Mockery::mock(ServicesContainer::class);
    /** @var \Mockery\MockInterface&Services */
    $this->runningServices = Mockery::mock(Services::class);

    /** @var \Mockery\MockInterface&DependenciesService */
    $this->dependenciesService = Mockery::mock(DependenciesService::class);
    /** @var \Mockery\MockInterface&DependenciesContainer */
    $this->dependenciesContainer = Mockery::mock(DependenciesContainer::class);


    /** @var \Mockery\MockInterface&Hooks */
    $this->hooks = Mockery::mock(Hooks::class); 

    $this->servicesContainer->shouldReceive('runningServices')->once()->andReturn($this->runningServices);

    $this->runningServices->shouldReceive('get')->once()->with('dependencies')->andReturn($this->dependenciesService);

    $this->dependenciesService->shouldReceive('container')->once()->andReturn($this->dependenciesContainer);

    $this->dependenciesContainer->shouldReceive('get')->once()->with(Hooks::class)->andReturn($this->hooks);

    $this->actionsService = new ActionsService;

});
it('uses the DependenciesContainer to get the registered Hooks and uses that instance to register the hooks', function() {
    $this->hooks->shouldReceive('register')->once();
    
    $this->actionsService->start($this->servicesContainer);
});

it('unregisters the hooks', function() {
    $this->hooks->shouldReceive('unregister')->once();
    
    $this->actionsService->stop($this->servicesContainer);
});