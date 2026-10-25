<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Language\InlineStatementsRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class InlineStatementsRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_multiple_statemets_at_once()
    {
        (object) $variableAssignment = $this->createMock(Renderable::class);
        (object) $methodCall = $this->createMock(Renderable::class);

        (object) $inlineStatements = new InlineStatementsRenderer([
            $variableAssignment,
            $methodCall
        ]);

        $variableAssignment->expects($this->once())
                           ->method('render')
                           ->willReturn('$name = "rafa"');

        $methodCall->expects($this->once())
                           ->method('render')
                           ->willReturn('$user->setName($name)');

        $this->assertSame('$name = "rafa";'."\n".'$user->setName($name);', $inlineStatements->render());
    }
}