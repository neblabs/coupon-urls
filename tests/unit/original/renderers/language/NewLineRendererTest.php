<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Language\NewLineRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class NewLineRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_rendres_new_line_after_the_other_renderer()
    {
        (object) $renderer = $this->createMock(Renderable::class);

        (object) $newline = new NewLineRenderer($renderer);
        (string) $rendererReturn = "\$name = 'rafa';";

        $renderer->expects($this->once())
                 ->method('render')
                 ->willReturn($rendererReturn);

        $this->assertSame("{$rendererReturn}\n", $newline->render());
    }

    public function test_rendres_new_line_after_the_other_renderer_direction_bottom()
    {
        (object) $renderer = $this->createMock(Renderable::class);

        (object) $newline = new NewLineRenderer($renderer);
        (string) $rendererReturn = "\$name = 'rafa';";

        $newline->setLineDirection('top');

        $renderer->expects($this->once())
                 ->method('render')
                 ->willReturn($rendererReturn);

        $this->assertSame("\n{$rendererReturn}", $newline->render());
    }

    public function test_rendres_nothing_if_given_renderer_renders_nothing()
    {
        (object) $renderer = $this->createMock(Renderable::class);

        (object) $newline = new NewLineRenderer($renderer);
        (string) $rendererReturn = "";

        $renderer->expects($this->once())
                 ->method('render')
                 ->willReturn($rendererReturn);

        $this->assertSame("", $newline->render());
    }
}