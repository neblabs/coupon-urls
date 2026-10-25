<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes;

use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\AutomaticallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\Abilities\ManuallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\PassingValidator;
use Throwable;

use function CouponURLs\Original\Utilities\Collection\a;

class SubscriberThatThrowsExceptionOnExecuteAndThatManuallyHandlesTheExceptionAndThrowsAnotherExceptionFake implements 
    Subscriber, 
    ManuallyHandleExceptions
{
    use DefaultPriority;

    public function createEventArguments(int $id, object $options) : EventArguments
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
        throw new ExceptionFake(
            'Opps, runtime exception inside execute(), should be handled manually by this very own subscriber!'
        );  
    }

    public function onException(Throwable $exception): mixed
    {
        throw new ExceptionFake(
            'thrown again inside onException, should NOT be handled anyomre by the EventHanlder'
        );
    } 
}
