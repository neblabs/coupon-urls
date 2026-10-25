<?php declare(strict_types=1);

use CouponURLs\App\Creation\Coupons\Couponurls\QueryParametersFromStringMatchingCurrentRequestFactory;
use CouponURLs\App\Creation\Uri\QueryParametersFromStringFactory;
use CouponURLs\App\Domain\CouponURLs\CouponURLs;
use CouponURLs\App\Domain\Uris\Abilities\URI;
use CouponURLs\App\Domain\Uris\QueryParameters;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Creation\Abilities\CreatableEntitiesWithParameters;
use CouponURLs\Original\Data\Query\Parameters;
use function CouponURLs\Original\Utilities\Collection\a;

/** @var UnitTestCase  */
it('createEntities() will create one entity if the query for the current request has all parameters of the query string from the given row', function() {
    /** @var \Mockery\MockInterface&QueryParametersFromStringFactory */
    (object) $queryParametersFromStringFactory = Mockery::mock(QueryParametersFromStringFactory::class);
    /** @var \Mockery\MockInterface&CreatableEntitiesWithParameters */
    (object) $couponURLsFromCouponIdFactory = Mockery::mock(CreatableEntitiesWithParameters::class);
    /** @var \Mockery\MockInterface&URI */
    (object) $requestURI = Mockery::mock(URI::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $requestQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $firstQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $secondQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $thirdQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&CouponURLs */
    (object) $returnedCouponURLs = Mockery::mock(CouponURLs::class);

    (object) $queryParametersFromStringMatchingCurrentRequestFactory = new QueryParametersFromStringMatchingCurrentRequestFactory(
        queryParametersFromStringFactory: $queryParametersFromStringFactory,
        couponURLsFromCouponIdFactory: $couponURLsFromCouponIdFactory,
        requestURI: $requestURI
    );

    (array) $rows = [
        a(id: 1, post_id: 10, meta_key: 'coupon_urls_query', meta_value: 'first'),
        a(id: 2, post_id: 20, meta_key: 'coupon_urls_query', meta_value: 'second'),
        a(id: 3, post_id: 30, meta_key: 'coupon_urls_query', meta_value: 'third')
    ];

    $requestURI->shouldReceive('queryParameters')->atLeast()->once()->andReturn($requestQueryParameters);
    ordered($queryParametersFromStringFactory->shouldReceive('create')->once()->with('first')->andReturn($firstQueryParameters));
    ordered($requestQueryParameters->shouldReceive('hasAllOf')->once()->with($firstQueryParameters)->andReturn(false));
    ordered($queryParametersFromStringFactory->shouldReceive('create')->once()->with('second')->andReturn($secondQueryParameters));
    ordered($requestQueryParameters->shouldReceive('hasAllOf')->once()->with($secondQueryParameters)->andReturn(true));

    $couponURLsFromCouponIdFactory->shouldReceive('createEntities')->once()->withArgs(fn(Collection $postIds) => $postIds->asArray() === [20])->andReturn($returnedCouponURLs);

    expect(
        $queryParametersFromStringMatchingCurrentRequestFactory->createEntities(
            $rows, 
            $parameters
        )
    )->toBe($returnedCouponURLs);

    $queryParametersFromStringFactory->shouldNotHaveReceived('create', ['third']);
});

/** @var UnitTestCase  */
it('createEntities() Will return an empty CouponURLs when no QueryParameters matched!', function() {
    /** @var \Mockery\MockInterface&QueryParametersFromStringFactory */
    (object) $queryParametersFromStringFactory = Mockery::mock(QueryParametersFromStringFactory::class);
    /** @var \Mockery\MockInterface&CreatableEntitiesWithParameters */
    (object) $couponURLsFromCouponIdFactory = Mockery::mock(CreatableEntitiesWithParameters::class);
    /** @var \Mockery\MockInterface&URI */
    (object) $requestURI = Mockery::mock(URI::class);
    /** @var \Mockery\MockInterface&Parameters */
    (object) $parameters = Mockery::mock(Parameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $requestQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $firstQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $secondQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&QueryParameters */
    (object) $thirdQueryParameters = Mockery::mock(QueryParameters::class);
    /** @var \Mockery\MockInterface&CouponURLs */
    (object) $returnedCouponURLs = Mockery::mock(CouponURLs::class);

    (object) $queryParametersFromStringMatchingCurrentRequestFactory = new QueryParametersFromStringMatchingCurrentRequestFactory(
        queryParametersFromStringFactory: $queryParametersFromStringFactory,
        couponURLsFromCouponIdFactory: $couponURLsFromCouponIdFactory,
        requestURI: $requestURI
    );

    (array) $rows = [
        a(id: 1, post_id: 10, meta_key: 'coupon_urls_query', meta_value: 'first'),
        a(id: 2, post_id: 20, meta_key: 'coupon_urls_query', meta_value: 'second'),
        a(id: 3, post_id: 30, meta_key: 'coupon_urls_query', meta_value: 'third')
    ];

    $requestURI->shouldReceive('queryParameters')->atLeast()->once()->andReturn($requestQueryParameters);
    ordered($queryParametersFromStringFactory->shouldReceive('create')->once()->with('first')->andReturn($firstQueryParameters));
    ordered($requestQueryParameters->shouldReceive('hasAllOf')->once()->with($firstQueryParameters)->andReturn(false));
    ordered($queryParametersFromStringFactory->shouldReceive('create')->once()->with('second')->andReturn($secondQueryParameters));
    ordered($requestQueryParameters->shouldReceive('hasAllOf')->once()->with($secondQueryParameters)->andReturn(false));
    ordered($queryParametersFromStringFactory->shouldReceive('create')->once()->with('third')->andReturn($thirdQueryParameters));
    ordered($requestQueryParameters->shouldReceive('hasAllOf')->once()->with($thirdQueryParameters)->andReturn(false));

    $couponURLsFromCouponIdFactory->shouldReceive('createEntities')->once()->withArgs(fn(Collection $postIds) => $postIds->asArray() === [])->andReturn($returnedCouponURLs);

    expect(
        $queryParametersFromStringMatchingCurrentRequestFactory->createEntities(
            $rows, 
            $parameters
        )
    )->toBe($returnedCouponURLs);
});

//test that it returns an empty entities when nothign was matched! VERY IMPORTANT!