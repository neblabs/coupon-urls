<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes;

use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\AutomaticallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\PassingValidator;

use function CouponURLs\Original\Utilities\Collection\a;

class SubscriberThatThrowsExceptionFake implements 
    Subscriber
{
    use DefaultPriority;

    public function createEventArguments() : EventArguments
    {
        return new EventArguments(a(
        ));
    }

    public function validator() : Validator
    {
        return new PassingValidator;
    }

    public function execute() : void
    {
    }
}