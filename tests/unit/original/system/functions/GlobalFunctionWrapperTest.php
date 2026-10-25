<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\System\Functions;

use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use WP_UnitTestCase;

Class GlobalFunctionWrapperTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...
    }
    
    /**
     * THE BEST WE CAN DO TO TEST THIS IS TO USE A SOLID BUILT IN FUNCTION
     * THAT'S BEEN AROUND FOR OVER TWO DECADES AND WE KNOW IT'S VERY UNLIKELY
     * TO EVER CHANGE.
     */
    public function test_calls_global_function_name_with_provided_arguments_and_returns_the_returned_value()
    {
        (object) $functionWrapper = new GlobalFunctionWrapper;
        (string) $string = 'one way!';

        (string) $result = $functionWrapper->str_replace(search: 'one', replace: 'two', subject: $string);

        $this->assertSame(expected: 'two way!', actual: $result);
    }
}