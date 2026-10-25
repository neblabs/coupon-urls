<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\SubscriberRequiresEventHandler;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Events\Wordpress\EventHandler;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\PassingValidator;

Class SubscriberRequiresEventHandlerFake implements Subscriber, SubscriberRequiresEventHandler {
    public function priority(): int
    {
        return 10;
    } 

    public function setEventHandler(EventHandler $eventHandler)
    {

    } 

    public function createEventArguments() : EventArguments
    {
        return new EventArguments([]);
    }

    public function validator() : Validator
    {
        return new PassingValidator;
    }

    public function execute() : void
    {

    }
}