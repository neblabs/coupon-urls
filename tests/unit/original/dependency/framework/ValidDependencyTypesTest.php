<?php declare(strict_types=1);

use CouponURLs\Original\Collections\PassedCollection;
use CouponURLs\Original\Dependency\Exceptions\InvalidDependencyException;
use CouponURLs\Original\Dependency\Framework\ValidDependencyTypes;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\CacheAndDependencyButNoStaticTypeFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\CacheButNoDependencyFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\CachedDependencyFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\UnCacheAndDependencyButNoStaticTypeFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\UnCacheButNoDependencyFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\UnCachedDependencyFake;
use CouponURLs\Tests\Unit\Original\Dependency\Fakes\ValidDependencyFake;

use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\a;

it('fails', function(array $dependencyTypes) {
    expect(fn() => new ValidDependencyTypes(
        new PassedCollection(_($dependencyTypes))
    ))->toThrow(InvalidDependencyException::class);
})->with(a(
    notADependency: a(
        dependencyTypes: [
            stdClass::class,
            ValidDependencyFake::class
        ],
    ),
    dependencyButNotCachedOrUncached: a(
        dependencyTypes: [
            MisingCachedOrUncachedDependencyFake::class,
            ValidDependencyFake::class
        ],
    ),
    implementsCacheButNoDependency: a(
        dependencyTypes: [
            CacheButNoDependencyFake::class,
            ValidDependencyFake::class
        ],
    ),
    implementsUnCachedButNoDependency: a(
        dependencyTypes: [
            UnCacheButNoDependencyFake::class,
            ValidDependencyFake::class
        ],
    ),
    implementsDependencyAndCachedButNoStaticType: a(
        dependencyTypes: [
            CacheAndDependencyButNoStaticTypeFake::class,
            ValidDependencyFake::class
        ],
    ),
    implementsDependencyAndUnCachedButNoStaticType: a(
        dependencyTypes: [
            UnCacheAndDependencyButNoStaticTypeFake::class,
            ValidDependencyFake::class
        ],
    )
));

it('passes and returns the dependencyTypes', function(array $dependencyTypes) {
    (object) $validDependencyTypes = new ValidDependencyTypes(
        new PassedCollection(_($dependencyTypes))
    );

    expect($validDependencyTypes->get()->asArray())->toBe($dependencyTypes);
})->with(a(
    cachedDependency: a(
        dependencyTypes: [
            CachedDependencyFake::class,
            ValidDependencyFake::class
        ]
    ),
    uncachedDependency: a(
        dependencyTypes: [
            UnCachedDependencyFake::class,
            ValidDependencyFake::class
        ]
    )
));
