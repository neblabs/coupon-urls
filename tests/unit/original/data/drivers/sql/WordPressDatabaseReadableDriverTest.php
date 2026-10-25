<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Data\Drivers\SQL\WordPressDatabaseReadableDriver;
use CouponURLs\Original\Data\Query\SQLParameters;
use CouponURLs\Original\Data\Schema\Structure;

use function CouponURLs\Original\Utilities\Collection\{a, _};
use function CouponURLs\Original\Utilities\Text\i;

it('findMany() calls wpdb::get_results with the correct placeholders and returns the result', function() {
    /** @var \Mockery\MockInterface&wpdb */
    (object) $wpdb = Mockery::mock(wpdb::class);
    (object) $databaseDriver = new WordPressDatabaseReadableDriver($wpdb);
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&SQLParameters */
    (object) $parameters = Mockery::mock(SQLParameters::class);
    (object) $queryString = i(
        'SELECT `wp_postmeta`.* FROM `wp_postmeta` WHERE (`wp_postmeta`.`meta_key` = :v1) AND (`wp_postmeta`.`meta_value` = :v2) AND (`wp_postmeta`.`post_id` = :v3)'
    );
    (object) $queryValues = _([
        ':v1' => 'events', // string
        ':v2' => '20', // numeric integer
        ':v3' => '28.99' // numeric float
    ]);

    (array) $results = a(
        a(id: 10, name: 'rafa'),
        a(id: 20, name: 'flavio'),
        a(id: 30, name: 'al')  
    );

    $parameters->shouldReceive('queryString')->atLeast()->once()->andReturn($queryString);
    $parameters->shouldReceive('queryValues')->atLeast()->once()->andReturn($queryValues);

    $wpdb->shouldReceive('prepare')->once()->with(
        'SELECT `wp_postmeta`.* FROM `wp_postmeta` WHERE (`wp_postmeta`.`meta_key` = %s) AND (`wp_postmeta`.`meta_value` = %d) AND (`wp_postmeta`.`post_id` = %f)',
        $queryValues->asArray()
    )->andReturn('mocked prepared query string');

    $wpdb->shouldReceive('get_results')->once()->with(
        'mocked prepared query string',
        ARRAY_A
    )->andReturn($results);

    expect($databaseDriver->findMany($parameters))
        ->toBeInstanceOf(Collection::class)
        ->toEqual(_($results));
});

it('findOne() calls wpdb::get_row with the correct placeholders and returns the result', function() {
    /** @var \Mockery\MockInterface&wpdb */
    (object) $wpdb = Mockery::mock(wpdb::class);
    (object) $databaseDriver = new WordPressDatabaseReadableDriver($wpdb);
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&SQLParameters */
    (object) $parameters = Mockery::mock(SQLParameters::class);
    (object) $queryString = i(
        'SELECT `wp_postmeta`.* FROM `wp_postmeta` WHERE (`wp_postmeta`.`meta_key` = :v1) AND (`wp_postmeta`.`meta_value` = :v2) AND (`wp_postmeta`.`post_id` = :v3)'
    );
    (object) $queryValues = _([
        ':v1' => 'events', // string
        ':v2' => '20', // numeric integer
        ':v3' => '28.99' // numeric float
    ]);

    (array) $result = a(id: 10, name: 'rafa');

    $parameters->shouldReceive('queryString')->atLeast()->once()->andReturn($queryString);
    $parameters->shouldReceive('queryValues')->atLeast()->once()->andReturn($queryValues);

    $wpdb->shouldReceive('prepare')->once()->with(
        'SELECT `wp_postmeta`.* FROM `wp_postmeta` WHERE (`wp_postmeta`.`meta_key` = %s) AND (`wp_postmeta`.`meta_value` = %d) AND (`wp_postmeta`.`post_id` = %f)',
        $queryValues->asArray()
    )->andReturn('mocked prepared query string');

    $wpdb->shouldReceive('get_row')->once()->with(
        'mocked prepared query string',
        ARRAY_A
    )->andReturn($result);

    expect($databaseDriver->findOne($parameters))
        ->toBeArray()
        ->toEqual($result);
});