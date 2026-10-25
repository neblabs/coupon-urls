<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\PassingValidationResult;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators;
use OutOfBoundsException;
use WP_UnitTestCase;

Class PassingValidationResultTest extends WP_UnitTestCase
{
    
    
        

    public function test_is_never_failing()
    {
        (object) $passingValidation = new PassingValidationResult;

        $this->assertFalse($passingValidation->isFailing());
    }

    /**
    * @dataProvider orwhenbooleanProvider
    */
    public function testAlwaysReturnsAValidationInstanceWhenCalled_OrWhen_boolean(bool $boolean) : void
    {
        (object) $passingValidation = new PassingValidationResult;

        $this->assertSame($passingValidation, $passingValidation->orWhen($boolean));
    }

    public function orwhenbooleanProvider()
    {
        return [
            'true' => [
                $true = true,
            ],
            'false' => [
                $false = false,
            ]
        ];   
    }
    

    public function testAlwaysReturnsAValidationInstanceWhenCalled_OrWhen_AndTheResultIsValid_Validator() : void
    {
        (object) $passingValidation = new PassingValidationResult;

        (object) $validator = $this->getMockForAbstractClass(Validator::class);

        $validator->expects($this->never())->method('execute');

        $this->assertSame($passingValidation, $passingValidation->orWhen($validator));
    }

    /**
    * @dataProvider andwhenbooleanProvider
    */
    public function test_AndWhen_boolean(bool $boolean, bool $isFailing) : void
    {
        (object) $passingValidation = new PassingValidationResult;

        $this->assertSame($isFailing, $passingValidation->andWhen($boolean)->isFailing());
    }

    public function andwhenbooleanProvider()
    {
        return [
            'true' => [
                $true = true,
                $isFailing = false
            ],
            'false' => [
                $false = false,
                $isFailing = true
            ]
        ];   
    }

    public function test_AndWhen_boolean_all_need_to_pass__fails() : void
    {
        (object) $passingValidation = new PassingValidationResult;
        (object) $validationResult = $passingValidation->andWhen(true)
                                                       ->andWhen(true)
                                                       ->andWhen(true)
                                                       ->andWhen(false)
                                                       ->andWhen(true);

        $this->assertSame(true, $validationResult->isFailing());
    }

    public function test_AndWhen_boolean_all_need_to_pass__passes() : void
    {
        (object) $passingValidation = new PassingValidationResult;
        (object) $validationResult = $passingValidation->andWhen(true)
                                                       ->andWhen(true)
                                                       ->andWhen(true)
                                                       ->andWhen(true)
                                                       ->andWhen(true);

        $this->assertSame(false, $validationResult->isFailing());
    }

    /**
    * @dataProvider andwhenvalidatorProvider
    */
    public function test_AndWhen_Validator(Validator $validator, bool $validatorIsValid, bool $isFailing) : void
    {
        (object) $passingValidation = new PassingValidationResult;

        $validator->expects($this->once())->method('isValid')->willReturn($validatorIsValid);

        $this->assertSame($isFailing, $passingValidation->andWhen($validator)->isFailing());
    }

    public function andwhenvalidatorProvider()
    {
        return [
            'true' => [
                $validator = $this->createMock(Validator::class),
                $validatorIsValid = true,
                $isFailing = false
            ],
            'false' => [
                $validator = $this->createMock(Validator::class),
                $validatorIsValid = false,
                $isFailing = true
            ]
        ];   
    }

    public function test_AndWhen_ValidatorFailsWitheACustomException() : void
    {
        (object) $passingValidation = new PassingValidationResult;
        (object) $validator = $this->createMock(Validator::class);
        (object) $customException = new OutOfBoundsException;

        $validator->expects($this->once())->method('isValid')->willReturn(false);
        $validator->expects($this->once())->method('getException')->willReturn($customException);

        (object) $validationResult = $passingValidation->andWhen($validator);

        $this->assertTrue($validationResult->isFailing());
        $this->assertSame($customException, $validationResult->getException());
    }
}