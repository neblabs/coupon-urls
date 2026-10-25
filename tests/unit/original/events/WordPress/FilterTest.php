<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use CouponURLs\Original\Construction\Event\Exceptions\ExceptionHandlerFactory;
use CouponURLs\Original\Construction\Events\EventHandlerFactory;
use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Events\Wordpress\Filter;
use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use CouponURLs\Original\Validation\Validator;
use Mockery;
use PHPUnit\Framework\Constraint\Callback;
use WP_UnitTestCase;

Class FilterTest extends WP_UnitTestCase
{
    public function test_the_value_from_the_subscriber_gets_correctly_returned()
    {

        (object) $passingValidator = $this->createMock(Validator::class);
        (object) $subscriber = new Class($passingValidator) Implements Subscriber {
            use DefaultPriority;

            public function __construct(
                public $passingValidator
            ) {}
            /**
             * THEY ALL NEED BE PUBLIC!
             */
            public function validator() : Validator
            {
                return $this->passingValidator;
            }

            public function createEventArguments(string $name) : EventArguments
            {
                return new EventArguments([
                    'userName' => $name
                ]);
            }

            public function execute(string $userName) : string
            {
                return "Hi, {$userName}";
            }
        };

        (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
        (object) $eventHandlerFactory = $this->createMock(EventHandlerFactory::class);
        (object) $eventHandler = new EventHandler($subscriber, Mockery::spy(ExceptionHandlerFactory::class));

        $globalFunctionWraper->expects($this->once())
                            ->method('__call')
                            ->with(
                                'add_filter',
                                new Callback(function($input) use (&$passedCallable) {
                                    $this->assertSame(expected: 'my_awesome_filter', actual: $input['hook_name']);
                                    $this->assertSame(expected: 10, actual: $input['priority']);
                                    $this->assertSame(expected: 1, actual: $input['accepted_args']);
                                    $this->assertInstanceof(\Closure::class, $input['callback']);
                                    $passedCallable = $input['callback'];
                                    return true;
                                })

                            );
        $eventHandlerFactory->expects($this->once())
                            ->method('create')
                            ->with($subscriber)
                            ->willReturn($eventHandler);

        $passingValidator->expects($this->once())
                        ->method('isValid')
                        ->willReturn(true);

        (object) $filter = new Filter(
            name: 'my_awesome_filter', 
            globalFunctionWraper: $globalFunctionWraper,
            eventHandlerFactory: $eventHandlerFactory
        );

        $filter->add(subscriber: $subscriber);

        $filter->register();

        $this->assertSame('Hi, rafa', $eventHandler->handle('rafa'));
    }
}