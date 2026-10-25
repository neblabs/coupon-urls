<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators\PassingValidator;

class SubscriberMissingCreateEventArgumentsFake implements Subscriber
{
    public function priority() : int
    {
        return 10;
    }

    public function validator() : Validator
    {
        return new PassingValidator;
    }

    public function execute() : void
    {

    }
}