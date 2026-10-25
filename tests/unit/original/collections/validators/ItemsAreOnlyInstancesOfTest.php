<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\Validators\ItemsAreOnlyInstancesOf;
use CouponURLs\Original\Exceptions\InvalidTypeException;

use function CouponURLs\Original\Utilities\Collection\{a, _};

it('thorws exception when given empty allowedTypes', function() {
    (object) $itemsAreOnlyInstancesOf = new ItemsAreOnlyInstancesOf(
        items: _((object) []),
        allowedTypes: _()
    );

    $itemsAreOnlyInstancesOf->validate();
})->throws(InvalidTypeException::class);

it('throws exception when at least one item is not an instace of the required types', function(Collection $items, Collection $allowedTypes) {
    (object) $itemsAreOnlyInstancesOf = new ItemsAreOnlyInstancesOf(
        items: $items,
        allowedTypes: $allowedTypes
    );

    $itemsAreOnlyInstancesOf->validate();
})->with(a(
    numbersOnly: a(
        items: _(1, 2, 3),
        allowedTypes: _(stdClass::class)
    ),
    numbersWithAValidType: a(
        items: _(new stdClass, 2, new stdClass),
        allowedTypes: _(stdClass::class)
    ),
    numbersWithAValidTypeOutOfTwo: a(
        items: _(new stdClass, 2, new stdClass),
        allowedTypes: _(iterable::class, stdClass::class)
    ),
    stringsOnly: a(
        items: _('one', 'two', 'three'),
        allowedTypes: _(stdClass::class)
    ),
    stringsWithAValidType: a(
        items: _(new stdClass, 'two', new stdClass),
        allowedTypes: _(stdClass::class)
    ),
    stringsWithAValidTypeOutOfTwo: a(
        items: _(new stdClass, 'two', new stdClass),
        allowedTypes: _(iterable::class, stdClass::class)
    ),
    objectsOnly: a(
        items: _(new ArrayObject, new ArrayObject, new ArrayObject),
        allowedTypes: _(stdClass::class)
    ),
    objectsWithAValidType: a(
        items: _(new stdClass, new ArrayObject, new stdClass),
        allowedTypes: _(stdClass::class)
    ),
    objectsWithAValidTypeOutOfTwo: a(
        items: _(new stdClass, new ArrayObject, new stdClass),
        allowedTypes: _(iterable::class, stdClass::class)
    )
))->throws(InvalidTypeException::class);


it('doesnt thorw an exception when all items are of the reqired types', function(Collection $items, Collection $allowedTypes) {
    (object) $itemsAreOnlyInstancesOf = new ItemsAreOnlyInstancesOf(
        items: $items,
        allowedTypes: $allowedTypes
    );

    $itemsAreOnlyInstancesOf->validate();
})->with(a(
    empty: a(
        items: _(),
        allowedTypes: _(stdClass::class, ArrayObject::class)
    ),
    oneItem: a(
        items: _(new stdClass),
        allowedTypes: _(stdClass::class, ArrayObject::class)
    ),
    twoItemsOfTheSameType: a(
        items: _(new stdClass, new stdClass),
        allowedTypes: _(stdClass::class, ArrayObject::class)
    ),
    twoItemsOfDiffernetAlowedType: a(
        items: _(new ArrayObject, new stdClass),
        allowedTypes: _(stdClass::class, ArrayObject::class)
    )
));