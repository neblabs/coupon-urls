<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Functions;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Language\NativeTypedVariableRenderer;
use CouponURLs\Original\Renderers\Language\VariableRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class NativeTypedVariableRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_a_string_type_argument()
    {
        (object) $variableRenderer = $this->createMock(VariableRenderer::class);
        (object) $NativeTypedVariableRenderer = new NativeTypedVariableRenderer($variableRenderer);

        $variableRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('$myVariable');

        $NativeTypedVariableRenderer->setType('string');

        $this->assertSame('string $myVariable', $NativeTypedVariableRenderer->render());
    }

    public function test_renders_a_custom_class_type_argument()
    {
        (object) $variableRenderer = $this->createMock(VariableRenderer::class);
        (object) $NativeTypedVariableRenderer = new NativeTypedVariableRenderer($variableRenderer);

        $variableRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('$myVariable');

        $NativeTypedVariableRenderer->setType('VariableRenderer');

        $this->assertSame('VariableRenderer $myVariable', $NativeTypedVariableRenderer->render());
    }
}