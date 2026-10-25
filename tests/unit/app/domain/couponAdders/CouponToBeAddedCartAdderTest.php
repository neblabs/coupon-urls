<?php declare(strict_types=1);

use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;

use function CouponURLs\Original\Utilities\Collection\a;

//sceanrios:
//cart has the coupon applied directly
//cart doesnt have the coupon
//to be added doesnt exist
//to be added exists


/** @var UnitTestCase  */
it('check whether it can be applied', function(bool $cartHasCoupon, bool $couponsToBeAddedHasCoupon, bool $canPerform) {
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&CouponsToBeAdded */
    (object) $couponsToBeAdded = Mockery::mock(CouponsToBeAdded::class);
    /** @var \Mockery\MockInterface&Cart */
    (object) $cart = Mockery::mock(Cart::class);

    (object) $couponToBeAddedAdder = new CouponToBeAddedCartAdder(
        coupon: $coupon,
        couponsToBeAdded: $couponsToBeAdded,
        cart: $cart
    );

    $cart->shouldReceive('has')->once()->with($coupon)->andReturn($cartHasCoupon);

    $couponsToBeAdded->shouldReceive('has')->with($coupon)->andReturn($couponsToBeAddedHasCoupon);

    expect($couponToBeAddedAdder->canPerform()->isValid())->toBe($canPerform);
})->with([
    'cart has the coupon, couponsToBeAdded doesnt' => a(
        cartHasCoupon: true,
        CouponsToBeAddedHasCoupon: false,
        canBeAdded: false
    ),
    'cart has the coupon, so does couponsToBeAdded? the latter shouldnt but just in case' => a(
        cartHasCoupon: true,
        CouponsToBeAddedHasCoupon: true,
        canBeAdded: false
    ),
    'cart does not have the coupon, neither does couponsToBeAdded' => a(
        cartHasCoupon: false,
        CouponsToBeAddedHasCoupon: false,
        canBeAdded: true
    ),
    'cart does not have the coupon, but couponsToBeAdded does' => a(
        cartHasCoupon: false,
        CouponsToBeAddedHasCoupon: true,
        canBeAdded: false
    ),
]);


/** @var UnitTestCase  */
it('adds the given coupon CouponsToBeAdded', function() {
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&CouponsToBeAdded */
    (object) $couponsToBeAdded = Mockery::mock(CouponsToBeAdded::class);
    /** @var \Mockery\MockInterface&Cart */
    (object) $cart = Mockery::mock(Cart::class);

    (object) $couponToBeAddedAdder = new CouponToBeAddedCartAdder(
        coupon: $coupon,
        couponsToBeAdded: $couponsToBeAdded,
        cart: $cart
    );

    $couponsToBeAdded->shouldReceive('add')->once()->with($coupon);

    $couponToBeAddedAdder->perform();
});