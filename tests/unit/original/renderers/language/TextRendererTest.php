<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Language\TextRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class TextRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_the_value_as_is()
    {
        (object) $text = new TextRenderer('my name is rafael');

        $this->assertSame('my name is rafael', $text->render());
    }
}