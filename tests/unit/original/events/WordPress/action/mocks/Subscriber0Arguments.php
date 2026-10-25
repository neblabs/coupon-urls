<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Action\Mocks;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Abilities\Validatable;
use Closure;

Class Subscriber0Arguments implements Subscriber {
    public function __construct(protected Closure $createMock) {}
    
    public function validator() : Validatable
    {
        return ($this->createMock)(Validatable::class);
    }

    protected function createEventArguments() : EventArguments
    {
        return new EventArguments(([]));
    }
    
    public function execute()
    {

    }
}