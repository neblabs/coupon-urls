<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Fakes;

use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\Abilities\AutomaticallyHandleExceptions;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\PassingValidator;

use function CouponURLs\Original\Utilities\Collection\a;

class SubscriberThatThrowsExceptionWhenInvalidArguemntsToCreateEventArgumentsFake implements 
    Subscriber, 
    AutomaticallyHandleExceptions
{
    use DefaultPriority;

    //MORE ARGUMENTS EXPECTED THAN WHAT WE WERE GIVEN
    public function createEventArguments(int $id, object $options, unexistentClass $unexistentClass, string $anotherNissingArgument) : EventArguments
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
        //..
    }
}