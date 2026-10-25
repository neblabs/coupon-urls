<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Action\Mocks;

use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Abilities\Validatable;
use Closure;

Class Subscriber3Arguments implements Subscriber {
    public function __construct(protected Closure $createMock) {}
    
    public function validator() : Validatable
    {
        return ($this->createMock)(Validatable::class);
    }

    protected function createEventArguments($postId, $postAuthor, $post) : EventArguments
    {
        return new EventArguments(([$postId, $postAuthor, $post]));
    }
    
    public function execute()
    {

    }
}