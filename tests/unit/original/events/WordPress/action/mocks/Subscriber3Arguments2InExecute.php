<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Action\Mocks;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Abilities\Validatable;
use Closure;
use WP_Post;

Class Subscriber3Arguments2InExecute implements Subscriber {
    public function __construct(protected Closure $createMock) {}
    
    public function validator() : Validatable
    {
        return ($this->createMock)(Validatable::class);
    }

    protected function createEventArguments($postId, $postAuthor, $post) : EventArguments
    {
        return new EventArguments([
            'identifier' => $postId,
            'theNewPost' => $post
        ]);
    }
    
    public function execute(WP_Post $theNewPost, int $identifier)
    {
               
    }
}