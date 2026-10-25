<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderer\Classes;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Classes\VisibilityRenderer;
use CouponURLs\Original\Renderers\Functions\FunctionRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class VisibilityRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_public_fucntion_by_default()
    {
        (object) $functionRenderer = $this->createMock(FunctionRenderer::class);

        (object) $method = new VisibilityRenderer($functionRenderer);

        $functionRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('function myFacyFunction()');

        $this->assertSame('public function myFacyFunction()', $method->render());
    }

    public function test_renders_protected_fucntion()
    {
        (object) $functionRenderer = $this->createMock(FunctionRenderer::class);

        (object) $method = new VisibilityRenderer($functionRenderer);

        $functionRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('function myFacyFunction()');

        $method->setVisibility('protected');

        $this->assertSame('protected function myFacyFunction()', $method->render());
    }
}