<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\Exceptions\ValidationException;
use CouponURLs\Original\Validation\ValidationResult;
use Exception;
use WP_UnitTestCase;

Class ValidationResultTest extends WP_UnitTestCase
{
    
    
        

    public function test_that_a_default_exception_is_set_on_an_empty_constructor()
    {
        (object) $validationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $this->assertInstanceOf(Exception::class, $validationResult->getException());
        $this->assertInstanceOf(ValidationException::class, $validationResult->getException());
    }

    public function test_a_custom_exception_is_set_in_the_constructor()
    {
        (object) $customException = new class extends Exception {};

        (object) $validationResult = $this->getMockBuilder(ValidationResult::class)
                                          ->setConstructorArgs([$customException])
                                          ->getMockForAbstractClass();

        $this->assertInstanceOf(Exception::class, $validationResult->getException());
        $this->assertSame($customException, $validationResult->getException());
    }

    public function test_a_custom_exception_is_set_using_the_setter_withexception()
    {
        (object) $customException = new class extends Exception {};

        (object) $validationResult = $this->getMockBuilder(ValidationResult::class)
                                          ->getMockForAbstractClass();

        $this->assertInstanceOf(Exception::class, $validationResult->getException());
        $this->assertNotSame($customException, $validationResult->getException());

        $validationResult->withException($customException);

        $this->assertSame($customException, $validationResult->getException());        
    }

    public function test_the_setter_withexception_returns_the_same_instance()
    {
        (object) $customException = new class extends Exception {};

        (object) $validationResult = $this->getMockBuilder(ValidationResult::class)
                                          ->getMockForAbstractClass();

        $result = $validationResult->withException($customException);

        $this->assertSame($validationResult, $result);
    }

    public function test_sets_and_return_itself_when_a_custom_exception_hasnt_been_set()
    {
        (object) $customException = new class extends Exception {};

        (object) $validationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $result = $validationResult->setExceptionWhenNoCustomExceptionHasBeenSet($customException);

        $this->assertSame($validationResult, $result);
        $this->assertSame($customException, $result->getException());
    }

    public function test_DOES_NOT_SET_but_returns_itself_when_a_custom_exception_has_ALREADY_been_set()
    {
        (object) $customException = new class extends Exception {};
        (object) $anotherCustomException = new class extends Exception {};

        (object) $validationResult = $this->getMockForAbstractClass(ValidationResult::class);

        $withExceptionResult = $validationResult->withException($anotherCustomException);
        $result = $validationResult->setExceptionWhenNoCustomExceptionHasBeenSet($customException);

        $this->assertSame($validationResult, $result);
        $this->assertSame($withExceptionResult, $result);

        // THE FIRST EXCEPTION THAT WE SET MUST BE USED, THE EXCPETION SET USING setExceptionWhenNoCustomExceptionHasBeenSet() SHOULD BE IGNORED AND DITCHED.
        $this->assertSame($anotherCustomException, $result->getException());
    }
}