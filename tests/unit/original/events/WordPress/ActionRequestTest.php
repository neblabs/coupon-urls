<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Action;
use CouponURLs\Original\Events\Wordpress\Request;
use WP_UnitTestCase;

Class ActionRequestTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...
    }
    
    public function test_creates_an_action_request()
    {
        (object) $actionRequest = new Request\Action(name: 'my_awesome_action');

        $this->assertSame(expected: Action::class, actual: $actionRequest->type());
        $this->assertSame(expected: 'my_awesome_action', actual: $actionRequest->name());
    }
}      