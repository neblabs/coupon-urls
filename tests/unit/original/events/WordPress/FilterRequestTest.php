<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Filter;
use CouponURLs\Original\Events\Wordpress\FilterRequest;
use CouponURLs\Original\Events\Wordpress\Request\Filter as RequestFilter;
use stdClass;
use WP_UnitTestCase;

Class FilterRequestTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...
    }
    
    public function test_creates_an_filter_request()
    {
        (object) $filterRequest = new RequestFilter(name: 'my_awesome_filter');

        $this->assertSame(expected: Filter::class, actual: $filterRequest->type());
        $this->assertSame(expected: 'my_awesome_filter', actual: $filterRequest->name());
    }
}