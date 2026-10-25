<?php declare(strict_types=1);

use CouponURLs\App\Creation\Coupons\CouponsFactory;
use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;
use CouponURLs\Original\Collections\Collection;
use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Text\i;

afterEach(fn() => wc()->session->cleanup_sessions());
beforeEach(function() : void {
   $this->couponsFactory = Mockery::mock(CouponsFactory::class); 
   $this->cart = Mockery::mock(Cart::class);
});
/** @var UnitTestCase  */
it('does not match when it has coupons but they weren"t added by the current instance', function() {
    (object) $session = new WC_Session_Handler;
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    $session->set('coupon_urls_coupons_to_be_added', value: ['FIRST', 'SECOND']);

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory);

    $coupon->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));

    expect($CouponsToBeAdded->has($coupon))->toBeTrue();
    expect($CouponsToBeAdded->hasAddedToTheQueue($coupon))->toBeFalse();
});

/** @var UnitTestCase  */
it('matches when the coupon was added by the current instance', function() {
    (object) $session = new WC_Session_Handler;
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon1 = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon3 = Mockery::mock(Coupon::class);

    $session->set('coupon_urls_coupons_to_be_added', value: ['FIRST', 'SECOND', 'THIRD']);

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory);

    $coupon1->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));
    $coupon3->shouldReceive('code')->atLeast()->once()->andReturn(i('THIRD'));

    $CouponsToBeAdded->add($coupon3);

    expect($CouponsToBeAdded->has($coupon1))->toBeTrue();
    expect($CouponsToBeAdded->hasAddedToTheQueue($coupon1))->toBeFalse();

    expect($CouponsToBeAdded->has($coupon3))->toBeTrue();
    expect($CouponsToBeAdded->hasAddedToTheQueue($coupon3))->toBeTrue();
});

/** @var UnitTestCase  */
it('adds a coupon, no duplicates', function() {
    (object) $session = new WC_Session_Handler;

    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon = Mockery::mock(Coupon::class);

    $coupon->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory);

    $CouponsToBeAdded->add($coupon);
    $CouponsToBeAdded->add($coupon);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe(['FIRST']);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe(['FIRST']);
});

/** @var UnitTestCase  */
it('adds two coupons in different calls, should have both', function() {
    (object) $session = new WC_Session_Handler;

    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon1 = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon2 = Mockery::mock(Coupon::class);

    $coupon1->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));
    $coupon2->shouldReceive('code')->atLeast()->once()->andReturn(i('SECOND'));

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory);

    $CouponsToBeAdded->add($coupon1);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe(['FIRST']);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe(['FIRST']);

    $CouponsToBeAdded->add($coupon2);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe(['FIRST', 'SECOND']);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe(['FIRST', 'SECOND']);
});

/** @var UnitTestCase  */
it('removes coupons', function() {
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon1 = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon2 = Mockery::mock(Coupon::class);
    (object) $couponCodes = _('FIRST', 'SECOND');
    (object) $session = new WC_Session_Handler;

    $coupon1->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));
    $coupon2->shouldReceive('code')->atLeast()->once()->andReturn(i('SECOND'));

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory, $couponCodes);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe(['FIRST', 'SECOND']);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe(['FIRST', 'SECOND']);

    $CouponsToBeAdded->remove($coupon1);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe(['SECOND']);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe(['SECOND']);

    $CouponsToBeAdded->remove($coupon2);

    expect($CouponsToBeAdded->couponCodes()->asArray())->toBe([]);
    expect($session->get('coupon_urls_coupons_to_be_added'))->toBe([]);
});

/** @var UnitTestCase  */
it('test has the coupons specified', function() {
    (object) $session = new WC_Session_Handler;
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon0 = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon1 = Mockery::mock(Coupon::class);
    /** @var \Mockery\MockInterface&Coupon */
    (object) $coupon2 = Mockery::mock(Coupon::class);

    $coupon0->shouldReceive('code')->atLeast()->once()->andReturn(i('ZERO'));
    $coupon1->shouldReceive('code')->atLeast()->once()->andReturn(i('FIRST'));
    $coupon2->shouldReceive('code')->atLeast()->once()->andReturn(i('SECOND'));

    (object) $CouponsToBeAdded = new CouponsToBeAdded($session, $this->couponsFactory, _('ZERO'));

    expect($CouponsToBeAdded->has($coupon0))->toBeTrue();
    expect($CouponsToBeAdded->has($coupon1))->toBeFalse();
    expect($CouponsToBeAdded->has($coupon2))->toBeFalse();

    $CouponsToBeAdded->add($coupon1);
    $CouponsToBeAdded->add($coupon2);

    expect($CouponsToBeAdded->has($coupon0))->toBeTrue();
    expect($CouponsToBeAdded->has($coupon1))->toBeTrue();
    expect($CouponsToBeAdded->has($coupon2))->toBeTrue();
});