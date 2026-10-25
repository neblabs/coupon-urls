<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Abilities\GettableCollection;
use CouponURLs\Original\Construction\Dependency\ProductionDependenciesContainerFactory;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Dependency;

use function CouponURLs\Original\Utilities\Collection\_;

it('creates a DependenciesContainer from a collection of dependnecy type strings', function() {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, Cached::class, StaticType::class);
    /** @var \Mockery\MockInterface&GettableCollection */
    (object) $dependencyTypesGetter = Mockery::mock(GettableCollection::class);
    (object) $productionDependenciesContainerFactory = new ProductionDependenciesContainerFactory;
    (object) $dependencyTypes = _(
        $dependency::class
    );

    $dependencyTypesGetter->shouldReceive('get')->once()->andReturn($dependencyTypes);

    expect($productionDependenciesContainerFactory->create($dependencyTypesGetter))
        ->toBeInstanceOf(DependenciesContainer::class);
});