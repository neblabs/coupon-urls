<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\GettableCollection;
use CouponURLs\Original\Construction\Dependency\ProductionDependenciesContainerFactory;
use CouponURLs\Original\Core\Abilities\ServicesContainer;
use CouponURLs\Original\Core\Services\DependenciesService;
use CouponURLs\Original\Dependency\DependenciesContainer;

beforeEach(function() {
   /** @var \Mockery\MockInterface&ServicesContainer */
    $this->servicesContainer = Mockery::mock(ServicesContainer::class);
    /** @var \Mockery\MockInterface&DependenciesContainer */
    $this->dependenciesContainer = Mockery::mock(DependenciesContainer::class);

    /** @var \Mockery\MockInterface&ProductionDependenciesContainerFactory */
    $this->dependenciesContainerFactory = Mockery::mock(ProductionDependenciesContainerFactory::class);
    /** @var \Mockery\MockInterface&GettableCollection */
    $this->dependencyTypes = Mockery::mock(GettableCollection::class);


    $this->dependenciesContainerService = new DependenciesService(
        dependenciesContainerFactory: $this->dependenciesContainerFactory,
        dependencyTypes: $this->dependencyTypes
    ); 
});

it('creates a new DependenciesContainer from the given Factory', function() {
    $this->dependenciesContainerFactory->shouldReceive('create')->once()->with($this->dependencyTypes)->andReturn($this->dependenciesContainer);

    $this->dependenciesContainerService->start($this->servicesContainer);

    expect($this->dependenciesContainerService->container())
        ->toBeInstanceOf(DependenciesContainer::class)
        ->toBe($this->dependenciesContainer);
});

it('destroys the dependenciesContainer instance when stopped', function() {
    $this->dependenciesContainerFactory->shouldReceive('create')->once()->with($this->dependencyTypes)->andReturn($this->dependenciesContainer);

    $this->dependenciesContainerService->start($this->servicesContainer);

    expect($this->dependenciesContainerService->container())
        ->toBeInstanceOf(DependenciesContainer::class)
        ->toBe($this->dependenciesContainer);

    $this->dependenciesContainerService->stop($this->servicesContainer);

    expect($this->dependenciesContainerService->container())->toBeNull();
});