<?php declare(strict_types=1);

use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;

use function CouponURLs\Original\Utilities\Collection\a;


/** @var UnitTestCase  */
it('check whether it can be applied', function(bool $couponIsValid, bool $canPerform) {
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Cart */
    (object) $cart = Mockery::mock(Cart::class);

    (object) $couponCartAdder = new CouponCartAdder(
        coupon: $coupon,
        cart: $cart
    );

    $coupon->shouldReceive('canBeApplied')->once()->andReturn($couponIsValid);

    //$couponsToBeAdded->shouldReceive('has')->with($coupon)->andReturn($couponsToBeAddedHasCoupon);

    expect($couponCartAdder->canPerform()->isValid())->toBe($canPerform);
})->with([
    'coupon is valid' => a(
        couponIsValid: true,
        canBeAdded: true
    ),
    'coupon is invalid' => a(
        couponIsValid: false,
        canBeAdded: false
    ),
]);

/** @var UnitTestCase  */
it('adds the coupon to the cart', function() {
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Cart */
    (object) $cart = Mockery::mock(Cart::class);

    (object) $couponCartAdder = new CouponCartAdder(
        coupon: $coupon,
        cart: $cart
    );

    $cart->shouldReceive('add')->once()->with($coupon);

    $couponCartAdder->perform();
});