<?php declare(strict_types=1);

use CouponURLs\Original\Data\Query\SQLParameters;
use CouponURLs\Original\Data\Schema\Fields;
use CouponURLs\Original\Data\Schema\Fields\ID;
use CouponURLs\Original\Data\Schema\Structure;
use Mockery\Mock;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;

use function CouponURLs\Original\Utilities\Text\i;
use function CouponURLs\Original\Utilities\Collection\_;

it('tests the query method always returns the same select instamce', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);

    (object) $sqlParameters = new SQLParameters($structure);

    $structure->shouldReceive('name');
    $structure->shouldReceive('fields')->andReturn($fields);
    $fields->shouldReceive('hasId')->andReturn(false);

    $sqlParameters->setBuilder(new GenericBuilder);

    (object) $firstQuery = $sqlParameters->query();

    // called multiple times, same instance returned
    expect($sqlParameters->query())->toBe($firstQuery);
    expect($sqlParameters->query())->toBe($firstQuery);
    expect($sqlParameters->query())->toBe($firstQuery);
});

it('sets the table name of the given structure before passing to driver', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    (object) $builder = new MySqlBuilder;
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);


    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);
    $structure->shouldReceive('name')->once()->andReturn('options_table');

    $fields->shouldReceive('hasId')->once()->andReturn(false);

    (object) $sqlParameters = new SQLParameters($structure);

    $sqlParameters->setBuilder($builder);

    $sqlParameters->beforePassingToDriver();

    expect($sqlParameters->queryString())
        ->toEqual(i('SELECT `options_table`.* FROM `options_table`'));
});

it('sets the columnn relation if the srtucture has an ID field.', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    (object) $builder = new MySqlBuilder;
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $idField = Mockery::mock(ID::class);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);
    $structure->shouldReceive('name')->once()->andReturn('wp_postmeta');

    $fields->shouldReceive('hasId')->once()->andReturn(true);
    $fields->shouldReceive('id')->atLeast()->once()->andReturn($idField);

    $idField->shouldReceive('name')->once()->andReturn(i('meta_key'));
    $idField->shouldReceive('id')->once()->andReturn(i('event'));

    (object) $sqlParameters = new SQLParameters($structure);

    $sqlParameters->setBuilder($builder);
    $sqlParameters->beforePassingToDriver();
    
    expect($sqlParameters->queryString())
        ->toEqual(
            i('SELECT `wp_postmeta`.* FROM `wp_postmeta` WHERE (`wp_postmeta`.`meta_key` = :v1)')
        );
        
    expect($sqlParameters->queryValues())->toEqual(_([
        ':v1' => i('event')
    ]));
});

it('cleans up the builder after being reset', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    (object) $sqlParameters = new SQLParameters($structure);

    $structure->shouldReceive('name')->atLeast()->once()->andReturn('options');

    $sqlParameters->setBuilder(new GenericBuilder);

    $sqlParameters->query()->where()->equals(column: 'id', value: 50);

    expect($sqlParameters->queryString()->get())->toBe('SELECT options.* FROM options WHERE (options.id = :v1)');
    expect($sqlParameters->queryValues()->asArray())->toBe([':v1' => 50]);

    $sqlParameters->reset();

    $sqlParameters->query()->where()->equals(column: 'name', value: 'isEnabled')
                                    ->equals(column: 'id', value: 175);

    expect($sqlParameters->queryString()->get())->toBe(
        'SELECT options.* FROM options WHERE (options.name = :v1) AND (options.id = :v2)'
    );
    expect($sqlParameters->queryValues()->asArray())->toBe([
        ':v1' => 'isEnabled',
        ':v2' => 175
    ]);
});