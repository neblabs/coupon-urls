<?php declare(strict_types=1);

namespace Tests\Unit\Original\Data\Model;

use ArrayObject;
use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Domain\Entities;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use Exception;
use UnexpectedValueException;
use WP_UnitTestCase;
use stdClass;

Class EntitiesTest extends WP_UnitTestCase
{
    
    
        

    public function test_throws_exception_when_gets_an_item_of_a_different_type_in_this_case_integer()
    {
        $this->expectException(UnexpectedValueException::class);

        (array) $items = [(object) [], 5];

        new class(new Collection($items)) extends Entities {
            protected function getDomainClass() : string
            {
                return stdClass::class;   
            }
            
        };
    }

    public function test_throws_exception_when_gets_an_item_of_a_different_type_in_this_case_another_class_type()
    {
        $this->expectException(UnexpectedValueException::class);

        (array) $items = [(object) [], new ArrayObject];

        new class(new Collection($items)) extends Entities {
            protected function getDomainClass() : string
            {
                return stdClass::class;   
            }
            
        };
    }

    public function test_throws_nothing_when_given_the_correct_types()
    {
        (array) $items = [(object) []];

        new class(new Collection($items)) extends Entities {
            protected function getDomainClass() : string
            {
                return stdClass::class;   
            }
            
        };
    }

    public function test_throws_nothing_when_given_the_correct_types_null_bug()
    {
        $this->expectException(UnexpectedValueException::class);

        (array) $items = [null];

        new class(new Collection($items)) extends Entities {
            protected function getDomainClass() : string
            {
                return stdClass::class;   
            }
            
        };
    }

    public function test_throws_nothing_when_given_the_correct_types_false_bug()
    {
        $this->expectException(UnexpectedValueException::class);
        
        (array) $items = [false];

        new class(new Collection($items)) extends Entities {
            protected function getDomainClass() : string
            {
                return stdClass::class;   
            }
            
        };
    }
}