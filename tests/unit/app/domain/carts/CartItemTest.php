<?php declare(strict_types=1);

use CouponURLs\App\Domain\Carts\CartItem;
use CouponURLs\App\Domain\Products\Product;

use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\a;

/** @var UnitTestCase  */
it('exports the correct data for a simple regular product', function() {
    /** @var \Mockery\MockInterface&WC_Product */
    (object) $wc_product = Mockery::mock(WC_Product::class);
    (object) $product = new Product($wc_product);
    (object) $cartItem = new CartItem(
        product: $product,
        quantity: 2,
        extraData: _(
            one: 'uno'
        )
    );

    $wc_product->shouldReceive('get_id')->atLeast()->once()->andReturn(10);
    $wc_product->shouldReceive('get_type')->atLeast()->once()->andReturn('simple');

    expect($cartItem->export()->asArray())->toBe(a(
        product_id: 10,
        quantity: 2,
        variation_id: 0,
        variation: [],
        cart_item_data: a(one: 'uno')
    ));
});

/** @var UnitTestCase  */
it('exports the correct data for a variation product', function() {
    /** @var \Mockery\MockInterface&WC_Product_Variation */
    (object) $wc_product = Mockery::mock(WC_Product_Variation::class);
    (object) $product = new Product($wc_product);
    (object) $cartItem = new CartItem(
        product: $product,
        quantity: 2,
        extraData: _(
            one: 'uno'
        )
    );

    $wc_product->shouldReceive('get_parent_id')->atLeast()->once()->andReturn(10);
    $wc_product->shouldReceive('get_id')->atLeast()->once()->andReturn(110);
    $wc_product->shouldReceive('get_variation_attributes')->atLeast()->once()->andReturn(a(
        color: 'red',
        size: 'l'
    ));
    $wc_product->shouldReceive('get_type')->atLeast()->once()->andReturn('variation');

    expect($cartItem->export()->asArray())->toBe(a(
        product_id: 10,
        quantity: 2,
        variation_id: 110,
        variation: a(
            color: 'red',
            size: 'l'
        ),
        cart_item_data: a(one: 'uno')
    ));
});
