<?php declare(strict_types=1);

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\Validators\CollectionHasItem;
use function CouponURLs\Original\Utilities\Collection\{_, a};

it('passes when has item, fails when it has and shouldHaveIt is false', function(Collection $items, mixed $item) {
    (object) $collectionHasItem = new CollectionHasItem(
        items: $items, 
        item: $item, 
        shouldHaveIt: true
    );

    (object) $collectionShouldNotHaveItem = new CollectionHasItem(
        items: $items, 
        item: $item, 
        shouldHaveIt: false
    );

    expect($collectionHasItem->isValid())->toBeTrue();
    expect($collectionShouldNotHaveItem->isValid())->toBeFalse();
})->with(
    a(
        string: a(
            items: _('one', 'two', 'three'),
            item: 'two'
        ),
        number: a(
            items: _(1, 2, 3),
            item: 2
        )
    )
);

it('fails when doesnt have item', function(Collection $items, mixed $item) {
    (object) $collectionHasItem = new CollectionHasItem(
        items: $items, 
        item: $item, 
        shouldHaveIt: true
    );

    (object) $collectionShouldNotHaveItem = new CollectionHasItem(
        items: $items, 
        item: $item, 
        shouldHaveIt: false
    );

    expect($collectionHasItem->isValid())->toBeFalse();
    expect($collectionShouldNotHaveItem->isValid())->toBeTrue();
})->with(
    a(
        string: a(
            items: _('one', 'two', 'three'),
            item: 'four'
        ),
        number: a(
            items: _(1, 2, 3),
            item: 4
        )
    )
);

[
    'string' => [
        'items' => ['one', 'two', 'three'],
        'item' => 'two'
    ],
    'number' => [
        'items' => [1, 2, 3],
        'item' => 2
    ]
];