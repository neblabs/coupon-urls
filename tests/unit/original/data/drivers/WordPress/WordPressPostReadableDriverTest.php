<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Data\WP_QueryFactory;
use CouponURLs\Original\Data\Drivers\Wordpress\WordPressPostReadableDriver;
use CouponURLs\Original\Data\Query\WordPressPostQueryParameters;

use function CouponURLs\Original\Utilities\Collection\_;

beforeEach(function() {
   /** @var \Mockery\MockInterface&WP_QueryFactory */
    $this->wp_queryFactory = Mockery::mock(WP_QueryFactory::class);
    /** @var \Mockery\MockInterface&WordPressPostQueryParameters */
    $this->wordpressPostQueryParameters = Mockery::mock(WordPressPostQueryParameters::class);
    /** @var \Mockery\MockInterface&WP_Query */
    $this->WP_Query = Mockery::mock(WP_Query::class); 
    $this->queryArguments = _(posts_per_page: 1);

    $this->wordpressPostReadableDriver = new WordPressPostReadableDriver($this->wp_queryFactory);
});

it('gets the first post found by the driver', function() {
    /** @var \Mockery\MockInterface&$WP_Post */
    (object) $WP_Post = Mockery::mock(WP_Post::class);

    $this->wordpressPostQueryParameters->shouldReceive('setLimitTo')->once()->with(1);
    $this->wordpressPostQueryParameters->shouldReceive('query')->once()->andReturn($this->queryArguments);

    $this->wp_queryFactory->shouldReceive('createWithArguments')->once()->with($this->queryArguments)->andReturn($this->WP_Query);

    $this->WP_Query->posts = [$WP_Post];

    expect($this->wordpressPostReadableDriver->findOne($this->wordpressPostQueryParameters))
        ->toBe($WP_Post);
});

it('gets all posts found by the driver', function() {
    /** @var \Mockery\MockInterface&$WP_Post */
    (object) $WP_Post1 = Mockery::mock(WP_Post::class);
    /** @var \Mockery\MockInterface&$WP_Post */
    (object) $WP_Post2 = Mockery::mock(WP_Post::class);
    (object) $queryArguments = _(
        post_type: 'page',
        posts_per_page: 50
    );

    $this->wordpressPostQueryParameters->shouldReceive('query')->once()->andReturn($queryArguments);

    $this->wp_queryFactory->shouldReceive('createWithArguments')->once()->with($queryArguments)->andReturn($this->WP_Query);

    $this->WP_Query->posts = [$WP_Post1, $WP_Post2];

    expect($this->wordpressPostReadableDriver->findMany($this->wordpressPostQueryParameters)->asArray())
        ->toBe([$WP_Post1, $WP_Post2]);
});
