<?php declare(strict_types=1);

use CouponURLs\Original\Characters\StringManager;
use CouponURLs\Original\Data\Schema\Abilities\StructureField;
use CouponURLs\Original\Data\Schema\Fields\ID;
use function CouponURLs\Original\Utilities\Text\i;

it('returns the name fo the given field', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field = Mockery::mock(StructureField::class);

    (object) $id = new ID($field, name: 'event');

    (object) $fieldName = i('the_field_name');

    $field->shouldReceive('name')->once()->andReturn($fieldName);

    expect($id->name())->toBe($fieldName);
});

it('returns the id as StringManager', function() {
    /** @var \Mockery\MockInterface&StructureField */
    (object) $field = Mockery::mock(StructureField::class);

    (object) $id = new ID($field, name: 'event');

    expect($id->id())->toBeInstanceOf(StringManager::class)->toEqual('event');
});