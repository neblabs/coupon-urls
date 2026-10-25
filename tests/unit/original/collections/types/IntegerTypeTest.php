<?php

namespace CouponURLs\tests\original\collections\types;

use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Collections\Mapper\Types;
use CouponURLs\Original\Collections\Mapper\Types\IntegerType;

Class IntegerTypeTest extends \WP_UnitTestcase
{
    public function test_has_no_minimum()
    {
        (object) $integerType = Types::INTEGER();

        $this->assertFalse($integerType->hasMinimum());
    }

    public function test_has_minimum()
    {
        (object) $integerType = Types::INTEGER()->minimum(1);

        $this->assertInstanceof(IntegerType::class, $integerType);
        $this->assertTrue($integerType->hasMinimum());
    }

    public function test_gets_minimum()
    {
        (object) $integerType = Types::INTEGER()->minimum(8);

        $this->assertTrue($integerType->hasMinimum());
        $this->assertIsInt($integerType->getMinimum());
        $this->assertSame(8, $integerType->getMinimum());
    }

    public function test_gets_minimum_always_integer()
    {
        (object) $integerType = Types::INTEGER()->minimum('nononon');

        $this->assertTrue($integerType->hasMinimum());
        $this->assertIsInt($integerType->getMinimum());
        $this->assertSame(1, $integerType->getMinimum());
    }

    public function test_sets_minimum_of_1_when_no_number_or_a_numver_less_than_1_is_given()
    {
        (object) $integerType = Types::INTEGER()->minimum(0);

        $this->assertTrue($integerType->hasMinimum());
        $this->assertSame(1, $integerType->getMinimum());
    }

    public function test_picks_minimum_since_its_been_defined_and_value_is_lower()
    {
        (object) $integerType = Types::INTEGER()->minimum(7);

        $this->assertSame(7, $integerType->getOrDefaultToMinimum(6));
    } 

    public function test_picks_value_since_min_its_been_defined_but_value_is_higher()
    {
        (object) $integerType = Types::INTEGER()->minimum(7);

        $this->assertSame(8, $integerType->getOrDefaultToMinimum(8));
    }    

    public function test_picks_value_since_no_min_has_been_defined()
    {
        (object) $integerType = Types::INTEGER();

        $this->assertSame(-1, $integerType->getOrDefaultToMinimum(-1));
    }    
}



