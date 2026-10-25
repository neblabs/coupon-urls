<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Abilities\ContextFactory;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\DynamicType;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Dependent;
use CouponURLs\Original\Dependency\KnownContext;
use CouponURLs\Original\Dependency\UnknownContext;
use CouponURLs\Tests\Unit\Original\Dependency\ClassWithOneArgumentStringFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassDependencyWithOneDependencyFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWith3DependenciesFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithAllClassArgumentsTyped;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithAnEmptyConstructorFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithNoConstrutorFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentArrayFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentBooleanFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentFloatFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentIntegerFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneArgumentNoTypeHintFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneCorrectArgumentAndAnotherNoTypeHintFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneValidArgumentAndAnotherArrayFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneValidArgumentAndAnotherBooleanFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneValidArgumentAndAnotherIntegerFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ClassWithOneValidArgumentAndAnotherStringFake;

use function CouponURLs\Original\Utilities\Collection\a;

it('gets the type from the given DynamicType', function() {
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);

    (object) $dependent = new Dependent(
        $dynamicType,
        $ContextFactory
    );

    $dynamicType->shouldReceive('type')->once()->andReturn('the type');

    expect($dependent->type())->toBe('the type');
});

it('can be created', function(string $className) {
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&KnownContext */
    (object) $context = Mockery::mock(KnownContext::class);

    (object) $dependent = new Dependent($dynamicType, $ContextFactory);

    $dynamicType->shouldReceive('type')->atLeast()->once()->andReturn($className);
    $dynamicType->shouldReceive('defaultType')->once()->andReturn($className);

    $ContextFactory->shouldReceive('create')->zeroOrMoreTimes()->withArgs(fn($reflectionParameter) => $reflectionParameter instanceof ReflectionParameter || throw  new Exception('ReflectionParameter not passed!'))->andReturn($context);

    expect($dependent->canBeCreated($context))->toBeTrue();
})->with(a(
    classExistsAndHasNoConstructor: a(
        className: ClassWithNoConstrutorFake::class
    ),
    classExistsAndHasConstructorNoArguments: a(
        className: ClassWithNoConstrutorFake::class
    ),
    classExistsAndHasConstructorAndAllArgumentsAreTyped: a(
        className: ClassWithAllClassArgumentsTyped::class
    )
));

it('cannot be created', function(string $className) {
   /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&UnknownContext */
    (object) $unknownContext = Mockery::mock(UnknownContext::class);

    (object) $dependent = new Dependent($dynamicType, $ContextFactory);

    $dynamicType->shouldReceive('type')->atLeast()->once()->andReturn($className);
    $dynamicType->shouldReceive('defaultType')->zeroOrMoreTimes()->andReturn($className);

    $ContextFactory->shouldReceive('create')->zeroOrMoreTimes()->andReturn($unknownContext);

    expect($dependent->canBeCreated($unknownContext))->toBeFalse();
})->with([
    'nonClassString' => a(
        className: 'idontexist'        
    ),
    'class has one argument: no type hint at all' => a(
        className: ClassWithOneArgumentNoTypeHintFake::class
    ),
    'class has one argument: string' => a(
        className: ClassWithOneArgumentStringFake::class
    ),
    'class has one argument: integer' => a(
        className: ClassWithOneArgumentIntegerFake::class
    ),
    'class has one argument: float' => a(
        className: ClassWithOneArgumentFloatFake::class
    ),
    'class has one argument: boolean' => a(
        className: ClassWithOneArgumentBooleanFake::class
    ),
    'class has one argument: array' => a(
        className: ClassWithOneArgumentArrayFake::class
    ),
    'class has one type (correct) but one non type' => a(
        className: ClassWithOneCorrectArgumentAndAnotherNoTypeHintFake::class
    ),
    'class has one type (correct) but one string' => a(
        className: ClassWithOneValidArgumentAndAnotherStringFake::class
    ),
    'class has one type (correct) but one integer' => a(
        className: ClassWithOneValidArgumentAndAnotherIntegerFake::class
    ),
    'class has one type (correct) but one boolean' => a(
        className: ClassWithOneValidArgumentAndAnotherBooleanFake::class
    ),
    'class has one type (correct) but one array' => a(
        className: ClassWithOneValidArgumentAndAnotherArrayFake::class
    ),
]);

it('creates a simple class With no constructor, USES THE DEFAULT TYPE!', function() {
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);

    (object) $dependent = new Dependent($dynamicType, $ContextFactory);

    $dynamicType->shouldReceive('defaultType')->atLeast()->once()->andReturn(ClassWithNoConstrutorFake::class);

    expect($dependent->create())
        ->toBeInstanceOf(ClassWithNoConstrutorFake::class)
        ->toEqual(new ClassWithNoConstrutorFake);
});

it('creates a simple class With a constructor but no args', function() {
    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);

    (object) $dependent = new Dependent($dynamicType, $ContextFactory);

    $dynamicType->shouldReceive('defaultType')->atLeast()->once()->andReturn(ClassWithAnEmptyConstructorFake::class);

    expect($dependent->create())
        ->toBeInstanceOf(ClassWithAnEmptyConstructorFake::class)
        ->toEqual(new ClassWithAnEmptyConstructorFake);
});

it('creates a class With class constructor arguments using the DependenciesContainer, PASSES THE CONTEXT TO THE DEPENDENCIESCONTAINER, DO NOT REMOVE!', function() {
    /** @var \Mockery\MockInterface&DependenciesContainer */
    (object) $dependenciesContainer = Mockery::mock(DependenciesContainer::class);
    /** @var \Mockery\MockInterface&ClassWithNoConstrutorFake */
    (object) $classWithNoConstrutorFake = Mockery::mock(ClassWithNoConstrutorFake::class);
    /** @var \Mockery\MockInterface&ClassDependencyWithOneDependencyFake */
    (object) $classDependencyWithOneDependencyFake = Mockery::mock(ClassDependencyWithOneDependencyFake::class);
    /** @var \Mockery\MockInterface&ClassWithAnEmptyConstructorFake */
    (object) $classWithAnEmptyConstructorFake = Mockery::mock(ClassWithAnEmptyConstructorFake::class);

    /** @var \Mockery\MockInterface&DynamicType */
    (object) $dynamicType = Mockery::mock(DynamicType::class);
    /** @var \Mockery\MockInterface&ContextFactory */
    (object) $ContextFactory = Mockery::mock(ContextFactory::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $classWithNoConstrutorContext = Mockery::mock(Context::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $classDependencyWithOneDependencyContext = Mockery::mock(Context::class);
    /** @var \Mockery\MockInterface&Context */
    (object) $classWithAnEmptyConstructorContext = Mockery::mock(Context::class);

    (object) $dependent = new Dependent($dynamicType, $ContextFactory);

    $dynamicType->shouldReceive('defaultType')->atLeast()->once()->andReturn(ClassWith3DependenciesFake::class);

    $dependent->setDependenciesContainer($dependenciesContainer);

    $ContextFactory->shouldReceive('create')->once()->withArgs(
        fn(ReflectionParameter $reflectionParameter) => ((string) $reflectionParameter->getType()) === ClassWithNoConstrutorFake::class
    )->andReturn($classWithNoConstrutorContext);

    $ContextFactory->shouldReceive('create')->once()->withArgs(
        fn(ReflectionParameter $reflectionParameter) => ((string) $reflectionParameter->getType()) === ClassDependencyWithOneDependencyFake::class
    )->andReturn($classDependencyWithOneDependencyContext);

    $ContextFactory->shouldReceive('create')->once()->withArgs(
        fn(ReflectionParameter $reflectionParameter) => ((string) $reflectionParameter->getType()) === ClassWithAnEmptyConstructorFake::class
    )->andReturn($classWithAnEmptyConstructorContext);

    $dependenciesContainer->shouldReceive('get')->once()->with(ClassWithNoConstrutorFake::class, $classWithNoConstrutorContext)->andReturn($classWithNoConstrutorFake);
    $dependenciesContainer->shouldReceive('get')->once()->with(ClassDependencyWithOneDependencyFake::class, $classDependencyWithOneDependencyContext)->andReturn($classDependencyWithOneDependencyFake);
    $dependenciesContainer->shouldReceive('get')->once()->with(ClassWithAnEmptyConstructorFake::class, $classWithAnEmptyConstructorContext)->andReturn($classWithAnEmptyConstructorFake);

    expect($dependent->create())
        ->toBeInstanceOf(ClassWith3DependenciesFake::class)
        ->toEqual(new ClassWith3DependenciesFake(
            $classWithNoConstrutorFake,
            $classDependencyWithOneDependencyFake,
            $classWithAnEmptyConstructorFake
        ));

});