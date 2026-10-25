<?php declare(strict_types=1);

use CouponURLs\App\Domain\Uris\QueryParameters;

use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

it('will match', function(array $subjectParameters, array $parametersToCheck, bool $matches) {
    (object) $subjectQueryParameters = new QueryParameters(_($subjectParameters));

    expect($subjectQueryParameters->hasAllOf(new QueryParameters(_($parametersToCheck))))
        ->toBe($matches);
})->with([
    'subject to check has none and parameters to check has none' => a(
        subjectParameters: [],
        parametersToCheck: [],
        matches: true
    ),
    'subject to check has one and parameters to check has none' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: [],
        matches: true
    ),
    'subject to check has one and parameters to check has the same' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: a(
            name: 'rafael'
        ),
        matches: true
    ),
    '(StringManager) subject to check has one and parameters to check has the same' => a(
        subjectParameters: a(
            name: i('rafael')
        ),
        parametersToCheck: a(
            name: i('rafael')
        ),
        matches: true
    ),
    '(StringManager first) subject to check has one and parameters to check has the same' => a(
        subjectParameters: a(
            name: i('rafael')
        ),
        parametersToCheck: a(
            name: 'rafael'
        ),
        matches: true
    ),
    '(StringManager last) subject to check has one and parameters to check has the same' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: a(
            name: i('rafael')
        ),
        matches: true
    ),
    'both same key and empty' => a(
        subjectParameters: a(
            name: ''
        ),
        parametersToCheck: a(
            name: ''
        ),
        matches: true
    ),
    'subject to check has two and parameters to check has one that matches' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna'
        ),
        parametersToCheck: a(
            first: 'rafael'
        ),
        matches: true
    ),
    'two and they both match' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna'
        ),
        parametersToCheck: a(
            first: 'rafael',
            last: 'serna'
        ),
        matches: true
    ),
    'first has three, second two and they match' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna',
            email: 'rafarks@gmail.com'
        ),
        parametersToCheck: a(
            first: 'rafael',
            last: 'serna'
        ),
        matches: true
    ),
    'first has three, second two and they match (different internal index)' => a(
        subjectParameters: a(
            last: 'serna',
            email: 'rafarks@gmail.com',
            first: 'rafael',
        ),
        parametersToCheck: a(
            first: 'rafael',
            last: 'serna'
        ),
        matches: true
    )
]);

it('will not match', function(array $subjectParameters, array $parametersToCheck, bool $matches) {
    (object) $subjectQueryParameters = new QueryParameters(_($subjectParameters));

    expect($subjectQueryParameters->hasAllOf(new QueryParameters(_($parametersToCheck))))
        ->toBe($matches);
})->with([
    'subject to check has none and parameters to check has one' => a(
        subjectParameters: [],
        parametersToCheck: a(name: 'rafael'),
        matches: false
    ),
    'subject to check has one and parameters to check has the same (KEY IS DIFFERNET CASE)' => a(
        subjectParameters: a(
            nAMe: 'rafael'
        ),
        parametersToCheck: a(
            name: 'rafael'
        ),
        matches: FALSE
    ),
    'different key and empty' => a(
        subjectParameters: a(
            last: ''
        ),
        parametersToCheck: a(
            name: ''
        ),
        matches: false
    ),
    'same key, first is empty and second is not' => a(
        subjectParameters: a(
            name: ''
        ),
        parametersToCheck: a(
            name: 'rafael'
        ),
        matches: false
    ),
    'same key, first is not empty and second is' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: a(
            name: ''
        ),
        matches: false
    ),
    'subject to check has one and parameters to check has one (same key but diff value)' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: a(
            name: 'rafa'
        ),
        matches: false
    ),
    'subject to check has one and parameters to check has one (diff key and value)' => a(
        subjectParameters: a(
            name: 'rafael'
        ),
        parametersToCheck: a(
            last: 'serna'
        ),
        matches: false
    ),
    'subject to check has one and parameters to check has two that matches (one matches but its not enough)' => a(
        subjectParameters: a(
            first: 'rafael',
        ),
        parametersToCheck: a(
            first: 'rafael',
            last: 'serna'
        ),
        matches: false
    ),
    'subject to check has one and parameters to check has two that matches (none match)' => a(
        subjectParameters: a(
            first: 'rafael',
        ),
        parametersToCheck: a(
            name: 'rafael',
            last: 'serna'
        ),
        matches: false
    ),
    'two and neither match (keys do)' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna'
        ),
        parametersToCheck: a(
            first: 'jeff',
            last: 'way'
        ),
        matches: false
    ),
    'two and neither match (values do)' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna'
        ),
        parametersToCheck: a(
            one: 'rafael',
            two: 'sern'
        ),
        matches: false
    ),
    'two and neither match' => a(
        subjectParameters: a(
            first: 'rafael',
            last: 'serna'
        ),
        parametersToCheck: a(
            one: 'uno',
            two: 'dos'
        ),
        matches: false
    ),
]);
