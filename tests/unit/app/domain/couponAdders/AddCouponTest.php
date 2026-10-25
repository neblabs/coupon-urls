<?php declare(strict_types=1);

use CouponURLs\App\Domain\Actions\CouponAdders\AddCoupon;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\Original\Validation\Validators\FailingValidator;
use CouponURLs\Original\Validation\Validators\PassingValidator;

use function CouponURLs\Original\Utilities\Collection\_;
/** @var UnitTestCase  */
it('CouponCartAdder gets called when passing', function() {
    /** @var \Mockery\MockInterface&CouponCartAdder */
    (object) $couponCartAdder = Mockery::mock(CouponCartAdder::class);
    /** @var \Mockery\MockInterface&CouponToBeAddedCartAdder */
    (object) $couponToBeAddedCartAdder = Mockery::mock(CouponToBeAddedCartAdder::class);
    (object) $passingValidator = new PassingValidator;
    (object) $failingValidator = new FailingValidator;

    (object) $couponAdder = new AddCoupon(_(
        $couponCartAdder,
        $couponToBeAddedCartAdder
    ));

    $couponCartAdder->shouldReceive('canPerform')->once()->andReturn($passingValidator);
    $couponToBeAddedCartAdder->shouldNotReceive('canPerform');

    $couponCartAdder->shouldReceive('perform')->once();
    $couponToBeAddedCartAdder->shouldNotReceive('perform');

    $couponAdder->perform();
});

it('CouponToBeAddedCartAdder gets called when passing and CouponCartAdder doesnt', function() {
    /** @var \Mockery\MockInterface&CouponCartAdder */
    (object) $couponCartAdder = Mockery::mock(CouponCartAdder::class);
    /** @var \Mockery\MockInterface&CouponToBeAddedCartAdder */
    (object) $couponToBeAddedCartAdder = Mockery::mock(CouponToBeAddedCartAdder::class);
    (object) $passingValidator = new PassingValidator;
    (object) $failingValidator = new FailingValidator;

    (object) $couponAdder = new AddCoupon(_(
        $couponCartAdder,
        $couponToBeAddedCartAdder
    ));

    $couponCartAdder->shouldReceive('canPerform')->once()->andReturn($failingValidator);
    $couponToBeAddedCartAdder->shouldReceive('canPerform')->once()->andReturn($passingValidator);

    $couponCartAdder->shouldNotReceive('perform');
    $couponToBeAddedCartAdder->shouldReceive('perform')->once();

    $couponAdder->perform();
});
