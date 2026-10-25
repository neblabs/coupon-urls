<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\Exceptions\ValidationException;
use CouponURLs\Original\Validation\FailingValidationResult;
use CouponURLs\Original\Validation\PassingValidationResult;
use CouponURLs\Original\Validation\ValidationResult;
use CouponURLs\Original\Validation\Validator;
use Exception;
use Mockery;
use OutOfRangeException;
use WP_UnitTestCase;

Class ValidatorTest extends WP_UnitTestCase
{
    
    
        

    public function test_throws_failing_exception_when_a_failingvalidation_object_is_returned()
    {
        $this->expectException(Exception::class);

        (object) $validator = Mockery::mock(Validator::class);

        $validator->shouldReceive('execute')->andReturn(new FailingValidationResult);

        $validator->validate();
    }



    public function test_validate_throws_exception_when_execute_returns_a_failing_result()
    {
        $this->expectException(Exception::class);

        (object) $validatorMock = $this->getMockForAbstractClass(Validator::class);
        (object) $failingValidationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $validatorMock->expects($this->once())
                      ->method('execute')
                      ->willReturn($failingValidationResult);

        $failingValidationResult->expects($this->once())
                                ->method('isFailing')
                                ->willReturn(true);

        $validatorMock->validate();
    }

    public function test_validate_does_not_throw_exception_when_execute_returns_a_passing_result()
    {
        (object) $validatorMock = $this->getMockForAbstractClass(Validator::class);
        (object) $passingValidationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $validatorMock->expects($this->once())
                      ->method('execute')
                      ->willReturn($passingValidationResult);

        $passingValidationResult->expects($this->once())
                                ->method('isFailing')
                                ->willReturn(false);

        $validatorMock->validate();
    }

    public function test_is_valid_fails_when_a_failing_validation_result_is_returned()
    {
        (object) $validatorMock = $this->getMockForAbstractClass(Validator::class);
        (object) $failingValidationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $validatorMock->expects($this->once())
                      ->method('execute')
                      ->willReturn($failingValidationResult);

        $failingValidationResult->expects($this->once())
                                ->method('isFailing')
                                ->willReturn(true);

        $this->assertFalse($validatorMock->isValid());
    }

    public function test_is_valid_passes_when_a_passing_validation_result_is_returned()
    {
        (object) $validatorMock = $this->getMockForAbstractClass(Validator::class);
        (object) $passingValidationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $validatorMock->expects($this->once())
                      ->method('execute')
                      ->willReturn($passingValidationResult);

        $passingValidationResult->expects($this->once())
                                ->method('isFailing')
                                ->willReturn(false);

        $this->assertTrue($validatorMock->isValid());
    }

    public function test_fails_when_extending_classes_use_failwhen_and_give_it_a_true_value()
    {
        (object) $validator = new class extends Validator {
            public function getDefaultException() : Exception
            {
                return new Exception;
            }
            
            public function execute() : ValidationResult
            {
                return $this->failWhen(true);
            }
        };

        $this->assertFalse($validator->isValid());
    }

    public function test_passes_when_extending_classes_use_failwhen_and_give_it_a_false_value()
    {
        (object) $validator = new class extends Validator {
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->failWhen(false);
            }
        };

        $this->assertTrue($validator->isValid());
    }

    public function test_passes_when_extending_classes_use_passwhen_and_give_it_a_true_value()
    {
        (object) $validator = new class extends Validator {
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen(true);
            }
        };

        $this->assertTrue($validator->isValid());
    }

    public function test_fails_when_extending_classes_use_passwhen_and_give_it_a_false_value()
    {
        (object) $validator = new class extends Validator {
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen(false);
            }
        };

        $this->assertFalse($validator->isValid());
    }

    public function testIsValidWhenGivingItAvalidtairAndThatValidatorIsValid()
    {
        (object) $testCase = $this;

        (object) $validator = new class($testCase) extends Validator {
            public function __construct(
                protected $testCase
            ) {}
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen($this->testCase->passingValidator);
            }
        };

        $this->passingValidator = new class($testCase) extends Validator {
            public function __construct(
                protected $testCase
            ) {}
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen(true);
            }
        };

        $this->assertTrue($validator->isValid());
    }

    public function testFailsWhenPassedValidatorFailsWithTheGivenException()
    {
        $this->expectException(OutOfRangeException::class);
        
        (object) $testCase = $this;

        (object) $validator = new class($testCase) extends Validator {
            public function __construct(
                protected $testCase
            ) {}
            public function getDefaultException() : Exception
            {
                return new Exception;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen($this->testCase->failingValidator);
            }
        };

        $this->failingValidator = new class($testCase) extends Validator {
            public function __construct(
                protected $testCase
            ) {}
            public function getDefaultException() : Exception
            {
                return new OutOfRangeException;
            }

            public function execute() : ValidationResult
            {
                return $this->passWhen(!true);
            }
        };

        $validator->validate();
    }
}