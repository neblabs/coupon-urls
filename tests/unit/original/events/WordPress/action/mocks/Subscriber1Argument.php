<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Action\Mocks;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Abilities\Validatable;
use Closure;

Class Subscriber1Argument implements Subscriber {
    public function __construct(protected Closure $createMock) {}
    
    public function validator() : Validatable
    {
        return ($this->createMock)(Validatable::class);
    }

    protected function createEventArguments($postId) : EventArguments
    {
        return new EventArguments([$postId]);
    }
    
    public function execute()
    {

    }
}