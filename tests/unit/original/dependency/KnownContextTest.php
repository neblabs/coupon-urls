<?php declare(strict_types=1);

use CouponURLs\App\Domain\Contents\Content;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Dependency\KnownContext;

use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

it('is true class Name', function(string $classToCheck, string $baseClass) {
    (object) $context = new KnownContext(
        fullyQualifiedTypeName: i($baseClass),
        methodName: i(''),
        variableName: i('')
    );

    expect($context->classIs($classToCheck))->toBeTrue();
})->with(a(
    fullyQualifiedNameMatches: a(
        classToCheck: KnownContext::class,
        baseClass: KnownContext::class
    ),
    fullyQualifiedNameMatchesDifferntCase: a(
        classToCheck: KnownContext::class,
        baseClass: 'CouponURLs\ORIGINAL\DEPENDENCY\KNOWNCONTEXT'
    )
));

it('is false class Name', function(string $classToCheck, string $baseClass) {
    (object) $context = new KnownContext(
        fullyQualifiedTypeName: i($baseClass),
        methodName: i(''),
        variableName: i('')
    );

    expect($context->classIs($classToCheck))->toBeFalse();
})->with(a(
    fullyQualifiedNameDoesntMatchShortName: a(
        classToCheck: KnownContext::class,
        baseClass: 'KnownContext'
    ),
    differentClasses: a(
        classToCheck: KnownContext::class,
        baseClass: Collection::class
    ),
));

it('is true name', function(string $variableNameToCheck, string $baseVariableName) {
    (object) $context = new KnownContext(
        fullyQualifiedTypeName: i($baseVariableName),
        methodName: i(''),
        variableName: i('')
    );

    expect($context->classIs($variableNameToCheck))->toBeTrue();
})->with(a(
    exactNameMatches: a(
        variableNameToCheck: 'product',
        baseVariableName: 'product'
    ),
    nameMatchesDifferentCase: a(
        variableNameToCheck: 'product',
        baseVariableName: 'prodUCT'
    )
));