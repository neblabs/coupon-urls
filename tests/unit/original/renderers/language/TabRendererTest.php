<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Language\TabRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class TabRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_contents_with_tab()
    {
        (object) $textRenderer = $this->createMock(Renderable::class);
        (object) $tab = new TabRenderer($textRenderer);

        $textRenderer->expects($this->once())
                     ->method('render')
                     ->willReturn('mytext');
                     
        $this->assertSame('    mytext', $tab->render());
    }
}