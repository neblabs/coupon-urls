<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Data\Drivers\Wordpress\WordPressPostArrayReadableDriver;
use CouponURLs\Original\Data\Drivers\Wordpress\WordPressPostReadableDriver;
use CouponURLs\Original\Data\Query\Parameters;

use function CouponURLs\Original\Utilities\Collection\{a, _};

it('uses WordPressPostReadableDriver to return an array instead of a wp_post', function() {
    /** @var \Mockery\MockInterface&WordPressPostReadableDriver */
    (object) $wordPressPostReadableDriver = Mockery::mock(WordPressPostReadableDriver::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&WP_Post */
    (object) $wp_post = Mockery::mock(WP_Post::class);

    (object) $wordPressPostArrayReadableDriver = new WordPressPostArrayReadableDriver(
        $wordPressPostReadableDriver
    );

    (array) $postDataArray = a(
        ID: 5,
        post_title: 'Hello world'
    );

    $wordPressPostReadableDriver->shouldReceive('findOne')->once()->with($parameters)->andReturn($wp_post);
    $wp_post->shouldReceive('to_array')->once()->andReturn($postDataArray);

    expect($wordPressPostArrayReadableDriver->findOne($parameters))->toBe($postDataArray);
});

it('uses WordPressPostReadableDriver to return an array or arrays instead of an array of wp_post instances', function() {
    /** @var \Mockery\MockInterface&WordPressPostReadableDriver */
    (object) $wordPressPostReadableDriver = Mockery::mock(WordPressPostReadableDriver::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&WP_Post */
    (object) $wp_post = Mockery::mock(WP_Post::class);
    /** @var \Mockery\MockInterface&WP_Post */
    (object) $wp_post2 = Mockery::mock(WP_Post::class);

    (object) $wordPressPostArrayReadableDriver = new WordPressPostArrayReadableDriver(
        $wordPressPostReadableDriver
    );

    (array) $postDataArray = a(
        ID: 5,
        post_title: 'Hello world'
    );
    (array) $secondpostDataArray = a(
        ID: 6,
        post_title: 'my first post'
    );

    $wordPressPostReadableDriver->shouldReceive('findMany')->once()->with($parameters)->andReturn(_(
        $wp_post, 
        $wp_post2
    ));

    $wp_post->shouldReceive('to_array')->once()->andReturn($postDataArray);
    $wp_post2->shouldReceive('to_array')->once()->andReturn($secondpostDataArray);

    expect($wordPressPostArrayReadableDriver->findMany($parameters))
        ->toBeInstanceOf(Collection::class)
        ->toEqual(_($postDataArray, $secondpostDataArray));
});
