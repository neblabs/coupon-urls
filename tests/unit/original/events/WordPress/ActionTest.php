<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Events\WordPress;

use CouponURLs\Original\Construction\Events\EventHandlerFactory;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Action;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use WP_UnitTestCase;

Class ActionTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        
        //...er t75
    }
    
    public function test_subscriber_gets_registered_to_the_correct_action_with_the_correct_parameters() : void
    {
        (object) $subscriber3Args = new Class Implements Subscriber {
            public function priority(): int
            {
                return 6;
            }

            public function createEventArguments(string $title, int $id, array $authors) : EventArguments
            {
                return new EventArguments([]);   
            }
        };
        (object) $subscriber1Args = new Class Implements Subscriber {
            public function priority(): int
            {
                return 12;
            }

            public function createEventArguments(array $authors) : EventArguments
            {
                return new EventArguments([]);
            }
        };

        (object) $subscriberNoArgs = new Class Implements Subscriber {
            public function priority(): int
            {
                return 2;
            }

            public function createEventArguments() : EventArguments
            {
                return new EventArguments([]);
            }
        };

        (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
        (object) $eventHandlerFactory = $this->createMock(EventHandlerFactory::class);

        $globalFunctionWraper->expects($this->exactly(1))
                             ->method('__call')
                             ->with(
                                    'add_action', 
                                    [
                                        'hook_name' => 'my_action_name', 
                                        'callback' => (fn() => "placeholder"), 
                                        'priority' => 6,
                                        'accepted_args' => 3 // 3
                                    ]
                            );

        [
            'add_action', 
            [
                'hook_name' => 'my_action_name', 
                'callback' => (fn() => "placeholder"), 
                'priority' => 12,
                'accepted_args' => 1 // 3
            ]
        ];
        [
            'add_action', 
            [
                'hook_name' => 'my_action_name', 
                'callback' => (fn() => "placeholder"), 
                'priority' => 2,
                'accepted_args' => 0 // 3
            ]
        ];

        $eventHandlerFactory->expects($this->exactly(1))
                            ->method('create')
                            ->with(
                                $subscriber3Args,
                                //[$subscriber1Args],
                                //[$subscriberNoArgs]
                            );

        (object) $action = new Action(
            name: 'my_action_name', 
            globalFunctionWraper: $globalFunctionWraper,
            eventHandlerFactory: $eventHandlerFactory
        );

        $action->add(subscriber: $subscriber3Args);
        //$action->add(subscriber: $subscriber1Args);
        //$action->add(subscriber: $subscriberNoArgs);

        $action->register();
    }

    public function _test_unregister_subscribers() : void
    {
        (object) $subscriber3Args = new Class Implements Subscriber {
            public function priority(): int
            {
                return 6;
            }

            public function createEventArguments(string $title, int $id, array $authors) : EventArguments
            {
                return new EventArguments([]);   
            }
        };
        (object) $subscriber1Args = new Class Implements Subscriber {
            public function priority(): int
            {
                return 12;
            }

            public function createEventArguments(array $authors) : EventArguments
            {
                return new EventArguments([]);
            }
        };

        (object) $subscriberNoArgs = new Class Implements Subscriber {
            public function priority(): int
            {
                return 2;
            }

            public function createEventArguments() : EventArguments
            {
                return new EventArguments([]);
            }
        };

        (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
        (object) $eventHandlerFactory = $this->createMock(EventHandlerFactory::class);

        $globalFunctionWraper->expects($this->exactly(1))
                             ->method('__call')
                             ->with(
                                    'add_action', 
                                    [
                                        'hook_name' => 'my_action_name', 
                                        'callback' => (fn() => "placeholder"), 
                                        'priority' => 6,
                                        'accepted_args' => 3 // 3
                                    ]
                            );

        $eventHandlerFactory->expects($this->exactly(1))
                            ->method('create')
                            ->with(
                                $subscriber3Args,
                                //[$subscriber1Args],
                                //[$subscriberNoArgs]
                            );

        (object) $action = new Action(
            name: 'my_action_name', 
            globalFunctionWraper: $globalFunctionWraper,
            eventHandlerFactory: $eventHandlerFactory
        );

        $action->add(subscriber: $subscriber3Args);
        //$action->add(subscriber: $subscriber1Args);
        //$action->add(subscriber: $subscriberNoArgs);

        $action->register();
        $globalFunctionWraper->expects($this->any())
                             ->method('__call')
                             ->with(
                                    'remove_action', 
                                    [
                                        'action' => 'my_action_name', 
                                        'function' => (fn() => "placeholder"), 
                                        'priority' => 6,
                                    ]
                            );

        $action->unregister();
    }
}