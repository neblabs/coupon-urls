<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderer\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderer\Language\InlineStatementRenderer;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class InlineStatementRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_an_inline_statemtet()
    {
        (object) $variableRenderer = $this->createMock(Renderable::class);

        (object) $inlineStatement = new InlineStatementRenderer($variableRenderer);

        $variableRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('$id');

        $this->assertSame('$id;', $inlineStatement->render());
    }
}