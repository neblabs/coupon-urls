<?php

namespace CouponURLs\Tests;

use AllowDynamicProperties;
use CouponURLs\App\Tests\Events\AutomatedEmailEntityCreator;
use CouponURLs\App\Tests\Events\PostEntityCreator;
use CouponURLs\App\Tests\Events\UserEntityCreator;
use CouponURLs\App\Tests\Events\WordPressEntitiesCreator;
use CouponURLs\App\Tests\Events\WordPressMailSentAsserter;
use CouponURLs\App\Tests\Events\WordPressUpdatePostRunner;
use CouponURLs\App\Tests\EventTestRunner;

#[AllowDynamicProperties]
abstract class IntegrationTestCase extends \WP_UnitTestCase
{
    public static DependenciesContainer $dependenciesContainer;
    
    public function set_up() : void
    {
        parent::set_up();
    }
    
    public function tear_down() : void
    {
        parent::tear_down();
        \Mockery::close();
    }
}