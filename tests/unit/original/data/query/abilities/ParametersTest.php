<?php declare(strict_types=1);

use CouponURLs\Original\Data\Query\Parameters;
use CouponURLs\Original\Data\Schema\Abilities\StructureField;
use CouponURLs\Original\Data\Schema\Fields;
use CouponURLs\Original\Data\Schema\Fields\Field;
use CouponURLs\Original\Data\Schema\Fields\ID;
use CouponURLs\Original\Data\Schema\Structure;

return;
it('automatically adds the id parameter when the structure has an ID field', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $id = Mockery::mock(ID::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);

    $fields->shouldReceive('hasId')->once()->andReturn(true);
    $fields->shouldReceive('id')->atLeast()->once()->andReturn($id);

    $id->shouldReceive('name')->once()->andReturn(_('key'));
    $id->shouldReceive('id')->once()->andReturn(_('5572'));

    (object) $parameters = new Parameters($structure);

    expect($parameters->count())->toBe(1);
    expect($parameters->get('key'))->toEqual('5572');
});

it('does not add an id parameter when the structure has no id field', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);

    $fields->shouldReceive('hasId')->once()->andReturn(false);
    $fields->shouldReceive('id')->never();

    (object) $parameters = new Parameters($structure);

    expect($parameters->count())->toBe(0);
});

it('gets the parameter using the real name', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);
    $fields->shouldReceive('hasId')->once()->andReturn(false);


    (object) $parameters = new Parameters($structure);

    $parameters->add('userId', value: 78);

    expect($parameters->get('userId'))->toBe(78);
});

it('gets the parameter using an aliased name', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $userField = Mockery::mock(StructureField::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);
    $fields->shouldReceive('hasId')->once()->andReturn(false);

    $fields->shouldReceive('has')->once()->with('user')->andReturn(true);
    $fields->shouldReceive('field')->once()->with('user')->andReturn($userField);

    $userField->shouldReceive('is')->once()->with('date')->andReturn(false);
    $userField->shouldReceive('is')->once()->with('uId')->andReturn(true);

    (object) $parameters = new Parameters($structure);

    $parameters->add('date', 2012);
    $parameters->add('uId', value: 78);

    //aliased
    expect($parameters->get('user'))->toBe(78);
    //real
    expect($parameters->get('uId'))->toBe(78);
});

it('INTEGRATION gets the parameter using an aliased name', function() {
    (object) $structure = new Class  extends Structure {
        public function name(): string
        {
            return 'user';
        }

        public function fields(): Fields
        {
            return new Fields(_([
                new Field(name: 'id', alias: 'user_id'),
                new Field(name: 'date', alias: 'creation_date')
            ]));
        } 
    };

    (object) $parameters = new Parameters($structure);

    $parameters->add('date', 2012);
    $parameters->add('id', value: 55);
    // the other way around!

    //aliased
    expect($parameters->get('user_id'))->toBe(55);
    //real
    expect($parameters->get('id'))->toBe(55);

    //real
    expect($parameters->get('date'))->toBe(2012);
    //alias
    expect($parameters->get('creation_date'))->toBe(2012);
});
it('returns true when the aliased name of a parameter is used', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&StructureField */
    (object) $userField = Mockery::mock(StructureField::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);
    $fields->shouldReceive('hasId')->once()->andReturn(false);

    $fields->shouldReceive('has')->once()->with('user')->andReturn(true);
    $fields->shouldReceive('field')->once()->with('user')->andReturn($userField);

    $userField->shouldReceive('is')->once()->with('date')->andReturn(false);
    $userField->shouldReceive('is')->once()->with('uId')->andReturn(true);

    (object) $parameters = new Parameters($structure);

    $parameters->add('date', 2012);
    $parameters->add('uId', value: 78);

    //aliased
    expect($parameters->has('user'))->toBeTrue();
    //real
    expect($parameters->has('uId'))->toBeTrue();
});