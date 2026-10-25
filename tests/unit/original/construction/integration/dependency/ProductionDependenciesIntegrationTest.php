<?php

use CouponURLs\Original\Collections\PassedCollection;
use CouponURLs\Original\Construction\Dependency\KnownContextFactory;
use CouponURLs\Original\Construction\Dependency\ProductionDependenciesContainerFactory;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Framework\ValidDependencyTypes;
use CouponURLs\Original\Dependency\KnownContext;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\AccountFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\AccountRequiresNothingFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\CachedDependencyRequiresDependenciesContainerFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\CachedDependencyWithOneResgiteredDependency;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\CustomerDependencyRequiresAccountFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\CustomerFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\DependentContextFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\DynamicClassFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\FirstDependencyOfTheSameTypeFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\Level1DependencyThatDependesOnTheLevel2DependencyFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\Level1DependentFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\Level2DependencyWithAnotherDependencyThatUsesContextFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\ProductDependencyRequiresCustomerFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\ProductFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\SameTypeFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\SecondDependencyOfTheSameTypeFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\SimpleCachedDependencyNoConstructorFake;

use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\o;

use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\SimpleClassFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\SimpleNamespacedDependencyNoConstructorFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\ThirdDependencyOfTheSameTypeFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\UserDataFake;
use CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes\UserFake;

beforeEach(function() {
   $this->dependenciesContainerFactory = new ProductionDependenciesContainerFactory; 
});

it('Simple Cached Dependency class With no constructor', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(stdClass::class);

    expect($user)->toBeInstanceOf(stdClass::class)
                ->toEqual(o(
                    id: 10,
                    name: 'rafael'
                ));
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependency). Registered Dependency is resgitered BEFORE', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                SimpleNamespacedDependencyNoConstructorFake::class,
                CachedDependencyWithOneResgiteredDependency::class,
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(UserFake::class);

    expect($user)->toBeInstanceOf(UserFake::class)
                ->toEqual(new UserFake(new UserDataFake));
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependency). Registered Dependency is resgitered AFTER', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                CachedDependencyWithOneResgiteredDependency::class,
                SimpleNamespacedDependencyNoConstructorFake::class,
            ))
        )    
    );

    (object) $user = $dependenciesContainer->get(UserFake::class);

    expect($user)->toBeInstanceOf(UserFake::class)
                ->toEqual(new UserFake(new UserDataFake));
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                ProductDependencyRequiresCustomerFake::class,
                CustomerDependencyRequiresAccountFake::class,
                AccountRequiresNothingFake::class
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY. ORDER: Product: 3, Customer: 2, Account: 4', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                CustomerDependencyRequiresAccountFake::class,
                ProductDependencyRequiresCustomerFake::class,
                AccountRequiresNothingFake::class
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});
it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY. ORDER: Product: 4, Customer: 2, Account: 3', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                CustomerDependencyRequiresAccountFake::class,
                AccountRequiresNothingFake::class,
                ProductDependencyRequiresCustomerFake::class,
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY. ORDER: Product: 2, Customer: 4, Account: 3', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                ProductDependencyRequiresCustomerFake::class,
                AccountRequiresNothingFake::class,
                CustomerDependencyRequiresAccountFake::class,
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});
it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY. ORDER: Product: 3, Customer: 4, Account: 2', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                AccountRequiresNothingFake::class,
                ProductDependencyRequiresCustomerFake::class,
                CustomerDependencyRequiresAccountFake::class,
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});

it('uses a Dependency class With a constructor with 1 argument (resgitered dependencies). Registered Dependency is ALSO REQUIRES A REGISTERED DEPENDENCY. ORDER: Product: 4, Customer: 3, Account: 2', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                AccountRequiresNothingFake::class,
                CustomerDependencyRequiresAccountFake::class,
                ProductDependencyRequiresCustomerFake::class,
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(ProductFake::class);

    expect($user)->toBeInstanceOf(ProductFake::class)
                ->toEqual(
                    new ProductFake(
                        new CustomerFake(
                            new AccountFake(10)
                        )
                    )
                );
});

it('can create instances from simple unregistered types that have no dependencies', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
            ))
        )
    );

    expect($dependenciesContainer->get(SimpleClassFake::class))
        ->toBeInstanceOf(SimpleClassFake::class)
        ->toEqual(new SimpleClassFake);
});

it('Dependency requires a DependenciesContainer', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                CachedDependencyRequiresDependenciesContainerFake::class
            ))
        )
    );

    (object) $user = $dependenciesContainer->get(DynamicClassFake::class);

    expect($user)->toBeInstanceOf(DynamicClassFake::class);
    expect($user->container)->toBe($dependenciesContainer);
});

it('3 dependencies with the same type, the 2nd passes due to a matching Context', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                SimpleCachedDependencyNoConstructorFake::class,
                FirstDependencyOfTheSameTypeFake::class,
                SecondDependencyOfTheSameTypeFake::class,
                ThirdDependencyOfTheSameTypeFake::class
            ))
        )
    );
    (object) $contextFactory = new KnownContextFactory;
    (object) $reflectionClass = new ReflectionClass(DependentContextFake::class);

    (object) $constructor = $reflectionClass->getConstructor();
    (object) $parameter = $constructor->getParameters()[0];

    (object) $object = $dependenciesContainer->get(
        type: SameTypeFake::class, 
        context: $contextFactory->create($parameter)
    );

    expect($object)->toBeInstanceOf(SameTypeFake::class);
    expect($object->position)->toBe('second');
});

it('BUG: known Context is not passed in a nested dependency', function() {
    /** @var DependenciesContainer */
    (object) $dependenciesContainer = $this->dependenciesContainerFactory->create(
        new ValidDependencyTypes(
            new PassedCollection(_(
                Level1DependencyThatDependesOnTheLevel2DependencyFake::class,
                Level2DependencyWithAnotherDependencyThatUsesContextFake::class,
            ))
        )
    );
    (object) $contextFactory = new KnownContextFactory;

    (object) $object = $dependenciesContainer->get(type: Level1DependentFake::class);

    expect($object)->toBeInstanceOf(Level1DependentFake::class);
    expect($object)->toEqual(new Level1DependentFake('rafa'));
});

// THERE NEED TO BE MORE TESTS TO TRY COVER MORE SCENATRIOS,
// 
// FOR EXAMPLE: 
//  WHEN A DEPENDENCY REUIRES AN ABSTRACT CLASS
//  WHEN A DEPENDENCY REQURES AN INTERFACE
//  WHEN TWO DEPENDENCIES REQUIRE THE SAME INTERFACE, 
//      BUT HAVE DIFFERENT canBeCreated() logic, so they both can coexist fine and they
//      both create the desired dependencies
//  
//  WHEN TWO DEPENDENCIES REQUIRE AN INTERFACE SAME 
//
// but its been weeks designing & implementing this layer, I'm overwhelmed.
// It works minimally as expected, for now.