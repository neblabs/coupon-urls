<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Functions;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Functions\FunctionArgumentsRenderer;
use CouponURLs\Original\Renderers\Language\NativeTypedVariableRenderer;
use CouponURLs\Original\Renderers\Language\VariableRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class FunctionArgumentsRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_no_arguemtns()
    {
        (object) $functionArgumentsRenderer = new FunctionArgumentsRenderer([]);

        $this->assertSame('', $functionArgumentsRenderer->render());
    }

    public function test_one_argument_not_typed()
    {
        (object) $variableRenderer = $this->getMockBuilder(VariableRenderer::class)
                                          ->setConstructorArgs(['myVariable'])
                                          ->getMock();

        $variableRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('$myVariable');

        (object) $functionArgumentsRenderer = new FunctionArgumentsRenderer([
            $variableRenderer
        ]);

        $this->assertSame('$myVariable', $functionArgumentsRenderer->render());
    }

    public function test_two_arguments_one_is_typed()
    {
        (object) $variableRenderer = $this->getMockBuilder(VariableRenderer::class)
                                          ->setConstructorArgs(['myVariable'])
                                          ->getMock();

        $variableRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('$myVariable');

        (object) $typedArgumentRenderer = $this->getMockBuilder(NativeTypedVariableRenderer::class)
                                          ->setConstructorArgs([$this->createMock(Renderable::class)])
                                          ->getMock();          
        
        $typedArgumentRenderer->expects($this->once())
                         ->method('render')
                         ->willReturn('string $name');

        (object) $functionArgumentsRenderer = new FunctionArgumentsRenderer([
            $variableRenderer,
            $typedArgumentRenderer
        ]);

        $this->assertSame('$myVariable, string $name', $functionArgumentsRenderer->render());
    }

    // test one with a regular argument, and witha typed argument
}