<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Data\Drivers\Abilities\SQLReadableDriver;
use CouponURLs\Original\Data\Drivers\Wordpress\WordPressPostMetaReadableDriver;
use CouponURLs\Original\Data\Query\Parameters;
use CouponURLs\Original\Data\Schema\Fields;
use CouponURLs\Original\Data\Schema\Fields\Field;
use CouponURLs\Original\Data\Schema\Structure;
use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use Mockery\Mock;
use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

return;

it('calls a WordPress function to get a single item, post id and key specified', function() {
    /** @var \Mockery\MockInterface&GlobalFunctionWrapper */
    (object) $globalFunctionWrapper = Mockery::mock(GlobalFunctionWrapper::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&SQLReadableDriver */
    (object) $sqlReadableDriver = Mockery::mock(SQLReadableDriver::class);
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostMetaReadableDriver = new WordPressPostMetaReadableDriver(
        $globalFunctionWrapper,
        $sqlReadableDriver
    );

    $parameters->shouldReceive('has')->once()->with('postId')->andReturn(true);
    $parameters->shouldReceive('get')->once()->with('postId')->andReturn(20);
    $parameters->shouldReceive('get')->atLeast()->once()->with('key')->andReturn('cp_is_enabled');

    $parameters->shouldReceive('structure')->atLeast()->once()->andReturn($structure);

    $globalFunctionWrapper->shouldReceive('get_post_meta')->once()->with(
        ...a(
            post_id: 20,
            key: 'cp_is_enabled',
            single: true
        )
    )->andReturn(
        'yes'
    );

    expect($wordpressPostMetaReadableDriver->findOne($parameters))
        ->toBeArray()
        ->toEqual(
            a(cp_is_enabled: 'yes')
        );
});

it('calls a WordPress function to get multiple items, post id and key specified', function() {
    /** @var \Mockery\MockInterface&GlobalFunctionWrapper */
    (object) $globalFunctionWrapper = Mockery::mock(GlobalFunctionWrapper::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&SQLReadableDriver */
    (object) $sqlReadableDriver = Mockery::mock(SQLReadableDriver::class);

    (object) $wordpressPostMetaReadableDriver = new WordPressPostMetaReadableDriver(
        $globalFunctionWrapper,
        $sqlReadableDriver
    );

    $parameters->shouldReceive('has')->once()->with('postId')->andReturn(true);
    $parameters->shouldReceive('get')->once()->with('postId')->andReturn(20);
    $parameters->shouldReceive('get')->atLeast()->once()->with('key')->andReturn('cp_is_enabled');

    $parameters->shouldReceive('has')->once()->with('key')->andReturn(true);

    $globalFunctionWrapper->shouldReceive('get_post_meta')->once()->with(
        ...a(
            post_id: 20,
            key: 'cp_is_enabled',
            single: true
        )
    )->andReturn(
        [
            'yes',
            'maybe'
        ]
    );

    expect(($wordpressPostMetaReadableDriver->findMany($parameters)))
        ->toBeInstanceOf(Collection::class)
        ->toEqual(
            _(
                a(cp_is_enabled: 'yes'),
                a(cp_is_enabled: 'maybe'),
            )
        );
});

it('only key specified, no postid, findmany', function() {
    /** @var \Mockery\MockInterface&GlobalFunctionWrapper */
    (object) $globalFunctionWrapper = Mockery::mock(GlobalFunctionWrapper::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&SQLReadableDriver */
    (object) $sqlReadableDriver = Mockery::mock(SQLReadableDriver::class);
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&Field */
    (object) $field = Mockery::mock(Field::class);

    (object) $wordpressPostMetaReadableDriver = new WordPressPostMetaReadableDriver(
        $globalFunctionWrapper,
        $sqlReadableDriver
    );

    $parameters->shouldReceive('has')->once()->with('postId')->andReturn(false);
    $parameters->shouldReceive('has')->once()->with('key')->andReturn(true);
    $parameters->shouldReceive('get')->atLeast()->once()->with('key')->andReturn('cp_is_enabled');
    $parameters->shouldReceive('structure')->once()->andReturn($structure);

    $structure->shouldReceive('name')->once()->andReturn('wp_postmeta');
    $structure->shouldReceive('fields')->once()->andReturn($fields);

    $fields->shouldReceive('field')->once()->with('key')->andReturn($field);

    $field->shouldReceive('name')->once()->andReturn(i('meta_key'));

    //PLEASE NOTE THAT INDENATION SHOUDL BE THE SAME AS IN getUsingWPDB()
    //IN ORDER FOR THE EXPECTATION TO MATCH (WHITESPACE, STRING)
    $parameters->shouldReceive('add')->once()->with(
            'query', 
            "
                SELECT * FROM wp_postmeta
                WHERE 'meta_key' = ?
            "
    );

    $parameters->shouldReceive('add')->once()->with(
        'parameters', 
        [
            'cp_is_enabled'
        ]
    );

    $sqlReadableDriver->shouldReceive('findMany')->with($parameters)->andReturn(_(
        a(meta_id: 78, post_id: 5, meta_key: 'cp_is_enabled', meta_value: 'yes'),
        a(meta_id: 85, post_id: 8, meta_key: 'cp_is_enabled', meta_value: 'yes'),
        a(meta_id: 89, post_id: 29, meta_key: 'cp_is_enabled', meta_value: 'no')
    ));

    expect($wordpressPostMetaReadableDriver->findMany($parameters))
        ->toBeInstanceOf(Collection::class)
        ->toEqual(
            _(
                a(cp_is_enabled: 'yes'),
                a(cp_is_enabled: 'maybe'),
            )
        ); 
});
it('calls multiple with no meta key specified')->todo();
