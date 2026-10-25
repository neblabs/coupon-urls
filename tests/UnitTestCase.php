<?php

namespace CouponURLs\Tests;

use CouponURLs\Original\Creation\Abilities\CreatableEntities;
use CouponURLs\App\Creation\Data\DataTemplateFactory;
use CouponURLs\App\Creation\Data\TemplateFactory;
use CouponURLs\App\Creation\Data\TextTemplateFactory;
use CouponURLs\App\Domain\Data\Abilities\DataSetCollection;
use CouponURLs\App\Domain\Data\TextTemplate;
use CouponURLs\App\Domain\Events\Event;
use CouponURLs\App\Tests\MockEventTestData;
use CouponURLs\App\Tests\MockEventTestHelper;
use CouponURLs\App\Tests\RealEventTestData;
use CouponURLs\Original\Construction\Events\HookFactory;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Events\Wordpress\SubscribersNotifier;
use Mockery;
use PHPUnit\Framework\TestCase;
use function CouponURLs\Original\Utilities\Collection\_;

abstract class UnitTestCase extends \WP_UnitTestCase
{
    public static DependenciesContainer $dependenciesContainer;

    public function set_up() : void
    {
        parent::set_up();
    }
    
    public function tear_down() : void
    {
        parent::tear_down();
        Mockery::close();
    }

}
