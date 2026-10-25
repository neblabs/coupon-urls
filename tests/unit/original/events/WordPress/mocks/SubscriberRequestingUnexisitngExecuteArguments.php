<?php

namespace CouponURLs\Tests\Unit\Original\Events\Wordpress\Mocks;

use CouponURLs\Original\Events\Parts\DefaultPriority;
use CouponURLs\Original\Events\Subscriber;
use CouponURLs\Original\Events\Wordpress\EventArguments;
use CouponURLs\Original\Validation\Abilities\Validatable;
use CouponURLs\Original\Validation\Validator;
use CouponURLs\Original\Validation\Validators;
use WP_Post;

Class SubscriberRequestingUnexisitngExecuteArguments implements Subscriber
{
    use DefaultPriority;
    
    public function validator() : Validator
    {
        return new Validators([]);
    }

    public function createEventArguments($post, $postId, $postTitle, $authors) : EventArguments
    {
        return new EventArguments([
            'createdPost' => $post,
            'newTitle' => $postTitle
        ]);
    }
    
    public function execute(WP_Post $post) : void
    {

    }

}