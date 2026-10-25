<?php declare(strict_types=1);

use CouponURLs\Original\Data\Schema\Abilities\StructureField;
use CouponURLs\Original\Data\Schema\Fields;
use CouponURLs\Original\Data\Schema\Fields\ID;

use function CouponURLs\Original\Utilities\Collection\_;

it('returns the field when name matches, could be real or could be an alias', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field3 = Mockery::mock(StructureField::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    $field1->shouldReceive('is')->once()->with('key')->andReturn(false);
    //StructureField::is() returns true for aliases.
    $field2->shouldReceive('is')->once()->with('key')->andReturn(true);
    $field3->shouldReceive('is')->never()->with('key');

    expect($fields->field('key'))->toBe($field2);
});

it('returns true when has a field with the given name', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field3 = Mockery::mock(StructureField::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    $field1->shouldReceive('is')->once()->with('key')->andReturn(false);
    //StructureField::is() returns true for aliases.
    $field2->shouldReceive('is')->once()->with('key')->andReturn(true);
    $field3->shouldReceive('is')->never()->with('key');

    expect($fields->has('key'))->toBeTrue();
});

it('returns false when has a field with the given name does not exist', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field3 = Mockery::mock(StructureField::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    $field1->shouldReceive('is')->once()->with('key')->andReturn(false);
    //StructureField::is() returns true for aliases.
    $field2->shouldReceive('is')->once()->with('key')->andReturn(false);
    $field3->shouldReceive('is')->once()->with('key')->andReturn(false);

    expect($fields->has('key'))->toBeFalse();
});

it('returns true when it has an ID field', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $field3 = Mockery::mock(ID::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    expect($fields->hasId())->toBeTrue();
});

it('returns false when it has no ID field', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field3 = Mockery::mock(StructureField::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    expect($fields->hasId())->toBeFalse();
});

it('returns the id', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field1 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field2 = Mockery::mock(StructureField::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $field3 = Mockery::mock(ID::class);

    (object) $fields = new Fields(_(
        $field1,
        $field2,
        $field3
    ));

    expect($fields->id())->toBe($field3);
});