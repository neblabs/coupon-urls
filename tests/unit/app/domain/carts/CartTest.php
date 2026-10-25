<?php declare(strict_types=1);

use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Products\Product;
use PHPUnit\Framework\MockObject\MockObject;
use function CouponURLs\Original\Utilities\Collection\a;
use function CouponURLs\Original\Utilities\Text\i;

/** @var UnitTestCase  */
it('returns true when it has the coupon', function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    (object) $cart = new Cart($wc_cart);

    $wc_cart->shouldReceive('get_applied_coupons')->once()->andReturn(['first']);

    $coupon->shouldReceive('code')->once()->andReturn(i('first'));

    expect($cart->has($coupon))->toBeTrue();
});

/** @var UnitTestCase  */
it('returns false when it doesnt have the coupon', function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    (object) $cart = new Cart($wc_cart);

    $wc_cart->shouldReceive('get_applied_coupons')->once()->andReturn(['first']);

    $coupon->shouldReceive('code')->once()->andReturn(i('second'));

    expect($cart->has($coupon))->toBeFalse();
});


/** @var UnitTestCase  */
it("returns true when we've added coupons and they exist in the cart", function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $goodCoupon = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $badCoupon = Mockery::mock(Coupon::class);


    (object) $cart = new Cart($wc_cart);

    $goodCoupon->shouldReceive('code')->atLeast()->once()->andReturn(i('good'));
    $badCoupon->shouldReceive('code')->atLeast()->once()->andReturn(i('bad'));

    // it "applies both" but internally one of them failed and wasnt added
    $wc_cart->shouldReceive('apply_coupon')->once()->with('good')->andReturn(true);
    $wc_cart->shouldReceive('apply_coupon')->once()->with('bad')->andReturn(false);
    //so it only really just has one
    $wc_cart->shouldReceive('get_applied_coupons')->atLeast()->once()->andReturn(['good']);

    $cart->add($goodCoupon);
    $cart->add($badCoupon);

    expect($cart->has($goodCoupon))->toBeTrue();
    expect($cart->has($badCoupon))->toBeFalse();

    expect($cart->hasAddedCoupon($goodCoupon))->toBeTrue();
    expect($cart->hasAddedCoupon($badCoupon))->toBeFalse();
});

/** @var UnitTestCase  */
it("returns the total quantity of non existing product", function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_b = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    (object) $productA = new Product($wc_product_a);
    (object) $productB = new Product($wc_product_b);

    (object) $cart = new Cart($wc_cart);

    (array) $cartContents = [
        'key087986' => a(
            quantity: 2,
            data: $wc_product_a
        ),
    ];

    $wc_product_a->shouldReceive('get_id')->andReturn(10);
    $wc_product_a->shouldReceive('get_parent_id')->andReturn(0);

    $wc_product_b->shouldReceive('get_id')->andReturn(20);
    $wc_product_b->shouldReceive('get_parent_id')->andReturn(0);

    $wc_cart->shouldReceive('get_cart_contents')->once()->andReturn($cartContents);

    expect($cart->quanititiesOf($productB))->toBe(0);
});

/** @var UnitTestCase  */
it("returns the total quantity of a specific product, simple product", function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_b = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    (object) $productA = new Product($wc_product_a);
    (object) $cart = new Cart($wc_cart);

    (array) $cartContents = [
        'key087986' => a(
            quantity: 2,
            data: $wc_product_a
        ),
        'key08798876576467' => a(
            quantity: 1,
            data: $wc_product_b
        ),
        'key897698696876858467' => a(
            quantity: 3,
            data: $wc_product_a
        )
    ];

    $wc_cart->shouldReceive('get_cart_contents')->once()->andReturn($cartContents);

    $wc_product_a->shouldReceive('get_id')->andReturn(10);
    $wc_product_a->shouldReceive('get_parent_id')->andReturn(0);

    $wc_product_b->shouldReceive('get_id')->andReturn(20);
    $wc_product_b->shouldReceive('get_parent_id')->andReturn(0);

    expect($cart->quanititiesOf($productA))->toBe(5);
});

/** @var UnitTestCase  */
it("returns the total quantity of a specific product, variable", function() {
    /** @var \Mockery\MockInterface&WC_Cart */
    (object) $wc_cart = Mockery::mock(WC_Cart::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_b = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_a = Mockery::mock(WC_Product_Simple::class);
    /** @var \Mockery\MockInterface&WC_Product_Simple */
    (object) $wc_product_c = Mockery::mock(WC_Product_Simple::class);

    (object) $productA = new Product($wc_product_a);
    (object) $productC = new Product($wc_product_c);

    (object) $cart = new Cart($wc_cart);

    (array) $cartContents = [
        'key087986' => a(
            quantity: 2,
            data: $wc_product_a
        ),
        'key08798876576467' => a(
            quantity: 1,
            data: $wc_product_b
        ),
        'key897698696876858467' => a(
            quantity: 3,
            data: $wc_product_a
        ),
        'key897698696876858467dthghtrjhtr' => a(
            quantity: 7,
            data: $wc_product_c
        )
    ];

    $wc_cart->shouldReceive('get_cart_contents')->andReturn($cartContents);

    $wc_product_a->shouldReceive('get_id')->andReturn(10);
    $wc_product_a->shouldReceive('get_parent_id')->andReturn(8);

    $wc_product_b->shouldReceive('get_id')->andReturn(20);
    $wc_product_b->shouldReceive('get_parent_id')->andReturn(0);

    $wc_product_c->shouldReceive('get_id')->andReturn(11);
    //same as the product a, but diff variation!
    $wc_product_c->shouldReceive('get_parent_id')->andReturn(8);

    expect($cart->quanititiesOf($productA))->toBe(5);
    expect($cart->quanititiesOf($productC))->toBe(7);
});