<?php declare(strict_types=1);

use CouponURLs\Original\Creation\Abilities\CreatableEntities;
use CouponURLs\Original\Creation\Abilities\CreatableEntitiesWithParameters;
use CouponURLs\Original\Data\Drivers\Abilities\ReadableDriver;
use CouponURLs\Original\Data\Model\Finder;
use CouponURLs\Original\Data\Query\Parameters;
use CouponURLs\Original\Domain\Entities;
use CouponURLs\Original\Domain\Entity;
use CouponURLs\Tests\Unit\Original\Data\Model\Fakes\FinderFake;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use function CouponURLs\Original\Utilities\Collection\{a, _};

it('findIt() calls the driver with the parameters and passes the result to the entityFactory. Calls Parameter events.', function() {
    /** @var \Mockery\MockInterface&ReadableDriver */
    (object) $readableDriver = Mockery::mock(ReadableDriver::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&CreatableEntitiesWithParameters */
    (object) $entityFactory = Mockery::mock(CreatableEntitiesWithParameters::class);

    (object) $finder = new class(
        readableDriver: $readableDriver,
        parameters: $parameters,
        entityFactory: $entityFactory
    ) extends Finder {};

    (array) $rawEntity = a(
        name: 'Rafael'
    );
    (object) $entity = Mockery::mock(Entity::class);

    $parameters->shouldReceive('beforePassingToDriver')->once()->globally()->ordered();
    $readableDriver->shouldReceive('findOne')->once()->globally()->ordered()->with($parameters)->andReturn($rawEntity);
    $entityFactory->shouldReceive('createEntity')->once()->globally()->ordered()->with($rawEntity, $parameters)->andReturn($entity);
    $parameters->shouldReceive('reset')->once()->globally()->ordered();

    expect($finder->findIt())->toBe($entity);
});

it('findThem() calls the driver with the parameters and passes the result to the entityFactory. Calls Parameter events.', function() {
    /** @var \Mockery\MockInterface&ReadableDriver */
    (object) $readableDriver = Mockery::mock(ReadableDriver::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&CreatableEntitiesWithParameters */
    (object) $entityFactory = Mockery::mock(CreatableEntitiesWithParameters::class);

    (object) $finder = new class(
        readableDriver: $readableDriver,
        parameters: $parameters,
        entityFactory: $entityFactory
    ) extends Finder {};

    (array) $rawEntities = (_(
        a(name: 'Rafael'),
        a(name: 'Flavio')
    ));

    (object) $entities = Mockery::mock(Entities::class);

    $parameters->shouldReceive('beforePassingToDriver')->once()->globally()->ordered();
    $readableDriver->shouldReceive('findMany')->once()->globally()->ordered()->with($parameters)->andReturn($rawEntities);
    $entityFactory->shouldReceive('createEntities')->once()->globally()->ordered()->with($rawEntities, $parameters)->andReturn($entities);
    $parameters->shouldReceive('reset')->once()->globally()->ordered();
    
    expect($finder->findThem())->toBe($entities);
});
