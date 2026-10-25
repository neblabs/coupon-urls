<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\Exceptions\InvalidTypeException;
use CouponURLs\Original\Validation\Exceptions\ValidationException;
use CouponURLs\Original\Validation\ValidationResult;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators;
use Exception;
use WP_UnitTestCase;
use stdClass;

Class ValidatorsTest extends WP_UnitTestCase
{
    
    
        

    protected $passingValidationResult;
    protected $failingValidationResult;

    public function setUp() : void
    {
        $this->passingValidationResult = $this->createMock(ValidationResult::class);
        $this->failingValidationResult = $this->createMock(ValidationResult::class);

        $this->passingValidationResult->expects($this->any())
                                      ->method('isFailing')
                                      ->willReturn(false);

        $this->failingValidationResult->expects($this->any())
                                      ->method('isFailing')
                                      ->willReturn(true);
    }
    
    public function test_fails_when_passed_objects_that_are_not_validators()
    {
        $this->expectException(InvalidTypeException::class);

        (object) $firstValidator = $this->createMock(Validator::class);
        (object) $secondValidator = $this->createMock(Validator::class);

        (object) $validators = new Validators([
            $firstValidator,
            new stdClass,
            $secondValidator
        ]);

        $validators->validate();
    }

    public function test_passes_when_all_validators_pass_array_given()
    {
        (object) $validator1 = $this->getMockForAbstractClass(Validator::class);
        (object) $validator2 = $this->getMockForAbstractClass(Validator::class);
        (object) $validator3 = $this->getMockForAbstractClass(Validator::class);

        (object) $validators = new Validators([
            $validator1,
            $validator2,
            $validator3
        ]);

        $validator1->expects($this->atLeastOnce())
                   ->method('execute')
                   ->willReturn($this->passingValidationResult);

        $validator2->expects($this->atLeastOnce())
                   ->method('execute')
                   ->willReturn($this->passingValidationResult);

        $validator3->expects($this->atLeastOnce())
                   ->method('execute')
                   ->willReturn($this->passingValidationResult);

        $validators->validate();

        $this->assertTrue($validators->isValid());
    }

    public function test_fails_when_one_of_the_validators_fail()
    {
        $this->expectException(Exception::class);

        (object) $validator1Passes = $this->getMockForAbstractClass(Validator::class);
        (object) $validator2Fails = $this->getMockForAbstractClass(Validator::class);
        (object) $validator3Passes = $this->getMockForAbstractClass(Validator::class);

        (object) $validators = new Validators([
            $validator1Passes,
            $validator2Fails,
            $validator3Passes
        ]);

        $validator1Passes->expects($this->atLeastOnce())
                   ->method('execute')
                   ->willReturn($this->passingValidationResult);

        $validator2Fails->expects($this->atLeastOnce())
                   ->method('execute')
                   ->willReturn($this->failingValidationResult);

        $validator3Passes->expects($this->never())
                   ->method('execute')
                   ->willReturn($this->passingValidationResult);

        $this->assertFalse($validators->isValid());
        //exception needs to be thrown after since everything after this wont be exectuted
        $validators->validate();
    }
}