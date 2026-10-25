<?php declare(strict_types=1);

use CouponURLs\App\Domain\Actions\Messages\CouponToBeAddedNotificationMessage;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;

/** @var UnitTestCase  */
it('wont be added if the coupon has not been added to the queue', function() {
    /** @var \Mockery\MockInterface&CouponsToBeAdded */
    (object) $couponsToBeAdded = Mockery::mock(CouponsToBeAdded::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    (object) $couponToBeAddedNotificationMessage = new CouponToBeAddedNotificationMessage(
        message: 'Your special offer will be applied when your cart is elegible',
        couponsToBeAdded: $couponsToBeAdded,
        coupon: $coupon        
    );

    $couponsToBeAdded->shouldReceive('hasAddedToTheQueue')->once()->with($coupon)->andReturn(false);

    expect($couponToBeAddedNotificationMessage->canPerform()->isValid())->toBeFalse();
});

/** @var UnitTestCase  */
it('will be added if the coupon has been added to the queue', function() {
    /** @var \Mockery\MockInterface&CouponsToBeAdded */
    (object) $couponsToBeAdded = Mockery::mock(CouponsToBeAdded::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    (object) $couponToBeAddedNotificationMessage = new CouponToBeAddedNotificationMessage(
        message: 'Your special offer will be applied when your cart is elegible',
        couponsToBeAdded: $couponsToBeAdded,
        coupon: $coupon        
    );

    $couponsToBeAdded->shouldReceive('hasAddedToTheQueue')->once()->with($coupon)->andReturn(true);
    
    expect($couponToBeAddedNotificationMessage->canPerform()->isValid())->toBeTrue();
});
