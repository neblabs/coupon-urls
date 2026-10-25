<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\GettableCollection;
use CouponURLs\Original\Collections\GettableCollectionDecorator;
use function CouponURLs\Original\Utilities\Collection\_;

it('ensures the contents of both getters are merged into a single collection', function() {
    /** @var \Mockery\MockInterface&GettableCollection */
    (object) $parentGettable = Mockery::mock(GettableCollection::class);
    /** @var \Mockery\MockInterface&GettableCollection */
    (object) $childGettable = Mockery::mock(GettableCollection::class);
    (array) $parentArray = [
        'one', 'two', 'three'
    ];
    (array) $childArray = [
        'four', 'five'
    ];

    (object) $gettableCollectionDecorator = new GettableCollectionDecorator(
        parentGettableCollection: $parentGettable,
        childGettableCollection: $childGettable
    );

    $parentGettable->shouldReceive('get')->once()->andReturn(_($parentArray));

    $childGettable->shouldReceive('get')->once()->andReturn(_($childArray));

    expect($gettableCollectionDecorator->get()->asArray())->toEqual([
        ...$parentArray,
        ...$childArray
    ]);
});
