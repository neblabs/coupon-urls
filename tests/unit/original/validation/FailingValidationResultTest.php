<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\FailingValidationResult;
use CouponURLs\Original\Validation\PassingValidationResult;
use CouponURLs\Original\Validation\Validator;
use OutOfBoundsException;
use WP_UnitTestCase;

it('test_is_always_failing', function()
{
    (object) $failingValidation = new FailingValidationResult;

    $this->assertTrue($failingValidation->isFailing());
});

it('test_orWhen_boolean', function(bool $boolean, bool $shouldFail) : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validationResult = $failingValidation->orWhen($boolean);

    $this->assertSame($shouldFail, $validationResult->isFailing());
})->with([
    'true' => [
        $boolean = true,
        $shouldFail = false
    ],
    'false' => [
        $boolean = false,
        $shouldFail = true
    ]
]);

it('tes orwhen will pass if one passes', function() : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validationResult = $failingValidation->orWhen(false)
                                                   ->orWhen(false)
                                                   ->orWhen(false)
                                                   ->orWhen(true)
                                                   ->orWhen(false);

    $this->assertSame(false, $validationResult->isFailing());
    expect($validationResult)->toBeInstanceOf(PassingValidationResult::class);
});

it('test_orWhen_Validator', function(Validator $validator, bool $validatorBooleanReturn, bool $shouldFail) : void
{
    (object) $failingValidation = new FailingValidationResult;

    $validator->expects($this->once())->method('isValid')->willReturn($validatorBooleanReturn);

    (object) $validationResult = $failingValidation->orWhen($validator);

    expect($validationResult->isFailing())->toBe($shouldFail);
})->with([
    'true' => [
        $validator = fn() => $this->createMock(Validator::class),
        $validatorBooleanReturn = true,
        $shouldFail = false
    ],
    'false' => [
        $validator = fn() => $this->createMock(Validator::class),
        $validatorBooleanReturn = false,
        $shouldFail = true
    ],
]);

it('gets the exception of the failing validator', function() : void
{
    (object) $failingValidation = new FailingValidationResult;
    (object) $customException = new OutOfBoundsException('cutsom failing exception for testing');

    (object) $validator = $this->createMock(Validator::class);

    $validator->expects($this->once())->method('isValid')->willReturn(false);
    $validator->expects($this->once())->method('getException')->willReturn($customException);

    (object) $validationResult = $failingValidation->orWhen($validator);

    expect($validationResult)->toBeInstanceOf(FailingValidationResult::class);
    expect($validationResult->getException())->toBe($customException);
});

it('tes orwhen will pass if one passes Validator', function() : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validator1 = $this->createMock(Validator::class);
    (object) $validator2 = $this->createMock(Validator::class);
    (object) $validator3 = $this->createMock(Validator::class);
    (object) $validator4 = $this->createMock(Validator::class);
    (object) $validator5 = $this->createMock(Validator::class);
    (object) $validator6 = $this->createMock(Validator::class);

    $validator1->expects($this->once())->method('isValid')->willReturn(false);
    $validator2->expects($this->once())->method('isValid')->willReturn(false);
    $validator3->expects($this->once())->method('isValid')->willReturn(false);
    $validator4->expects($this->once())->method('isValid')->willReturn(true); // passes
    $validator5->expects($this->never())->method('isValid')->willReturn(false);
    $validator6->expects($this->never())->method('isValid')->willReturn(false);

    (object) $validationResult = $failingValidation->orWhen($validator1)
                                                   ->orWhen($validator2)
                                                   ->orWhen($validator3)
                                                   ->orWhen($validator4) // passes
                                                   ->orWhen($validator5)
                                                   ->orWhen($validator6);

    expect($validationResult)->toBeInstanceOf(PassingValidationResult::class);
});

it('test_andWhen_boolean (always fails)', function(bool $boolean) : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validationResult = $failingValidation->andWhen($boolean);

    $this->assertTrue($validationResult->isFailing());
})->with([
    'true' => [
        $boolean = true,
    ],
    'false' => [
        $boolean = false,
    ]
]);

it('tes andwhen will always fails even if one passes', function() : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validationResult = $failingValidation->andWhen(false)
                                                   ->andWhen(true)
                                                   ->andWhen(true)
                                                   ->andWhen(true)
                                                   ->andWhen(true);

    $this->assertTrue($validationResult->isFailing());
    expect($validationResult)->toBeInstanceOf(FailingValidationResult::class);
});

it('tes andwhen all fail', function() : void
{
    (object) $failingValidation = new FailingValidationResult;

    (object) $validationResult = $failingValidation->andWhen(false)
                                                   ->andWhen(false)
                                                   ->andWhen(false)
                                                   ->andWhen(false)
                                                   ->andWhen(false);

    $this->assertTrue($validationResult->isFailing());
    expect($validationResult)->toBeInstanceOf(FailingValidationResult::class);
});