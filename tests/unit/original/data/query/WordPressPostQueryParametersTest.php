<?php declare(strict_types=1);

use CouponURLs\Original\Data\Query\WordPressPostQueryParameters;
use CouponURLs\Original\Data\Schema\Fields;
use CouponURLs\Original\Data\Schema\Fields\ID;
use CouponURLs\Original\Data\Schema\Structure;
use function CouponURLs\Original\Utilities\Collection\_;

use function CouponURLs\Original\Utilities\s;
use function CouponURLs\Original\Utilities\Text\i;

it('sets internal relationship if structure has an id before passing to driver, -1 limit is always the default', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $idField = Mockery::mock(ID::class);
    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);

    $fields->shouldReceive('hasId')->once()->andReturn(true);
    $fields->shouldReceive('id')->once()->andReturn($idField);

    $idField->shouldReceive('id')->once()->andReturn(i('automatedemails'));

    $wordpressPostQueryParameters->beforePassingToDriver();

    expect($wordpressPostQueryParameters->query())
        ->toEqual(
            _(
                post_type: ['automatedemails'],
                posts_per_page: -1
            )
        );
});

it('sets nothing when structure has NO id before passing to driver, limit is the default', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);
    /** @var \Mockery\MockInterface&Fields */
    (object) $fields = Mockery::mock(Fields::class);
    /** @var \Mockery\MockInterface&ID */
    (object) $idField = Mockery::mock(ID::class);
    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $structure->shouldReceive('fields')->atLeast()->once()->andReturn($fields);

    $fields->shouldReceive('hasId')->once()->andReturn(false);
    $fields->shouldReceive('id')->never()->andReturn();

    $idField->shouldReceive('name')->never()->andReturn(i('automatedemails'));

    $wordpressPostQueryParameters->beforePassingToDriver();

    expect($wordpressPostQueryParameters->query())
        ->toEqual(
            _(
                posts_per_page: -1
            )
        );
});

it('sets the post type', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->setPostType('product');

    expect($wordpressPostQueryParameters->query())
        ->toEqual(_(
            post_type: ['product'],
            posts_per_page: -1
        ));
});

it('sets a custom limit', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->setLimitTo(50);

    expect($wordpressPostQueryParameters->query())
        ->toEqual(_(
            posts_per_page: 50
        ));
});

it('test adds a meta value', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->setMetaValue('red');

    expect($wordpressPostQueryParameters->query())->toEqual(
        _(
            meta_value: 'red',
            posts_per_page: -1
        )
    );
});

it('test converts to string if a property is an object', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'i was an object';
        }  
    };

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->shouldBeANumber = 10;
    $wordpressPostQueryParameters->shouldBeALiteralString = 'one';
    $wordpressPostQueryParameters->shouldBeAConvertedStringable = $stringable;
    $wordpressPostQueryParameters->shouldBeAnArray = ['two'];

    expect($wordpressPostQueryParameters->query())->toEqual(_(
        shouldBeANumber: 10,
        shouldBeALiteralString: 'one',
        shouldBeAConvertedStringable: 'i was an object',
        shouldBeAnArray: ['two'],
        posts_per_page: -1
    ));
    expect($wordpressPostQueryParameters->query()->get('shouldBeAConvertedStringable'))
        ->toBeString();
});

it('resets the parameters, limit should be reset too', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->setPostType('product');
    $wordpressPostQueryParameters->setMetaValue('red');

    expect($wordpressPostQueryParameters->query())
        ->toEqual(_(
            post_type: ['product'],
            meta_value: 'red',
            posts_per_page: -1
        ));

    $wordpressPostQueryParameters->reset();

    expect($wordpressPostQueryParameters->query())->toEqual(_(posts_per_page: -1));
});

it('gets the meta value', function() {
    /** @var \Mockery\MockInterface&Structure */
    (object) $structure = Mockery::mock(Structure::class);

    (object) $wordpressPostQueryParameters = new WordPressPostQueryParameters($structure);

    $wordpressPostQueryParameters->setMetaValue('red');

    expect($wordpressPostQueryParameters->metaValue())->toBe('red');
});