<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Construction\Events;

use CouponURLs\Original\Construction\Events\HookFactory;
use CouponURLs\Original\Events\Wordpress\Action;
use CouponURLs\Original\Events\Wordpress\Request;
use PHPUnit\Framework\MockObject\MockObject;
use WP_UnitTestCase;

Class HookFactoryTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...
    }
    
    public function test_creates_an_action()
    {
        (object) $hookFactory = new HookFactory;
        /** @var Request\Action|MockObject */
        (object) $actionRequest = $this->getMockBuilder(Request\Action::class)
                                       ->setConstructorArgs(['myaction'])
                                       ->getMock();
        $actionRequest->expects($this->once())
                      ->method('type')
                      ->willReturn(Action::class);

        $actionRequest->expects($this->once())
                        ->method('name')
                        ->willReturn('myaction');

        (object) $action = $hookFactory->createFromRequest($actionRequest);

        $this->assertInstanceOf(Action::class, $action);
    }
}