<?php

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Validation\Validators\CollectionHasKey;

beforeEach(function() : void {
    $this->collection = new Collection(['name' => 'Rafa!']);
});

it('is invalid when it doesnt have the given key', function() {
    (object) $collectionHasKey = new CollectionHasKey(
        collection: $this->collection,
        key: 'id'
    );

    expect($collectionHasKey->isValid())->toBeFalse();
});

it('is valid when it has the given key', function() {
    (object) $collectionHasKey = new CollectionHasKey(
        collection: $this->collection,
        key: 'name'
    );

    expect($collectionHasKey->isValid())->toBeTrue();
});