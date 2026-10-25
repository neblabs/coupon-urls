<?php declare(strict_types=1);

use CouponURLs\App\Creation\Uri\QueryParametersFromStringFactory;
use CouponURLs\App\Domain\Uris\QueryParameters;

use function CouponURLs\Original\Utilities\Collection\{a, _};

/** @var UnitTestCase  */
it('creates a queryparameters instance from the given string', function(string $parametersString, array $expectedParameters) {
    (object) $queryParametersFromStringFactory = new QueryParametersFromStringFactory;

    expect($queryParametersFromStringFactory->create($parametersString))->toEqual(new QueryParameters(_($expectedParameters)));
})->with([
    'empty string' => a(
        parametersString: '',
        expectedParameters: []
    ),
    'simple with question mark' => a(
        parametersString: '?name=rafael',
        expectedParameters: a(name: 'rafael')
    ),
    'simple without question mark' => a(
        parametersString: 'name=rafael',
        expectedParameters: a(name: 'rafael')
    ),
    'two with question mark' => a(
        parametersString: '?name=rafael&last=serna',
        expectedParameters: a(name: 'rafael', last: 'serna')
    ),
    'two without question mark' => a(
        parametersString: 'name=rafael&last=serna',
        expectedParameters: a(name: 'rafael', last: 'serna')
    ),
    'one does not have a value without question mark' => a(
        parametersString: 'name=rafael&enabled&last=serna',
        expectedParameters: a(name: 'rafael', enabled: '', last: 'serna')
    ),
    'WILL MAKE THE KEYS AND VALUES CASE INSENSITIVE' => a(
        parametersString: 'nAMe=RAFAEL&LasT=SErnA',
        expectedParameters: a(name: 'rafael', last: 'serna')
    ),
]);