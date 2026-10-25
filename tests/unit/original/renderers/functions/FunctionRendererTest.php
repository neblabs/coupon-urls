<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Functions;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Functions\FunctionRenderer;
use CouponURLs\Original\Renderers\Functions\FunctionArgumentsRenderer;
use CouponURLs\Original\Renderers\Functions\FunctionReturnTypeRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class FunctionRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_renders_a_function_no_name_and_no_nothing()
    {
        (object) $function = new FunctionRenderer;

        (string) $expectedfunction = 
    "function() 
    {
        
    }";

        $this->assertSame($expectedfunction, $function->render());
    }

    public function test_renders_a_function_no_no_body_no_arguments_no_return_tyrpe_no_nothing()
    {
        (object) $function = new FunctionRenderer();
        $function->setName('execute');

        (string) $expectedfunction = 
    "function execute() 
    {
        
    }";

        $this->assertSame($expectedfunction, $function->render());
    }

    public function test_renders_a_function_no_no_body_no_arguments()
    {
        (object) $function = new FunctionRenderer;
        (object) $functionReturnTypeRenderer = $this->createMock(FunctionReturnTypeRenderer::class);

        $function->setName('execute');
        $function->setReturnTypeRenderer($functionReturnTypeRenderer);

        $functionReturnTypeRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn(': string');

        (string) $expectedfunction = 
    "function execute() : string
    {
        
    }";

        $this->assertSame($expectedfunction, $function->render());
    }

    public function test_renders_a_function_no_no_body()
    {
        (object) $functionArgumentsRenderer = $this->getMockBuilder(FunctionArgumentsRenderer::class)
                                                   ->setConstructorArgs([[$this->createMock(Renderable::class)]])
                                                   ->getMock();

        (object) $functionReturnTypeRenderer = $this->createMock(FunctionReturnTypeRenderer::class);

        (object) $function = new FunctionRenderer();

        $function->setName('execute');
        $function->setReturnTypeRenderer($functionReturnTypeRenderer);
        $function->setArgumentsRenderer($functionArgumentsRenderer);

        $functionArgumentsRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn('string $name');

        $functionReturnTypeRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn(': string');

        (string) $expectedfunction = 
    "function execute(string \$name) : string
    {
        
    }";

        $this->assertSame($expectedfunction, $function->render());
    }

    public function test_renders_a_function_with_body()
    {
        (object) $functionArgumentsRenderer = $this->getMockBuilder(FunctionArgumentsRenderer::class)
                                                   ->setConstructorArgs([[$this->createMock(Renderable::class)]])
                                                   ->getMock();

        (object) $functionReturnTypeRenderer = $this->createMock(FunctionReturnTypeRenderer::class);
        (object) $bodyRenderer = $this->createMock(Renderable::class);

        (object) $function = new FunctionRenderer;

        $function->setName('execute');
        $function->setReturnTypeRenderer($functionReturnTypeRenderer);
        $function->setArgumentsRenderer($functionArgumentsRenderer);
        $function->setBodyRenderer($bodyRenderer);

        $functionArgumentsRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn('string $name');

        $functionReturnTypeRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn(': string');

        $bodyRenderer->expects($this->once())
                                  ->method('render')
                                  ->willReturn('echo "I was generated!";');
        (string) $expectedfunction = 
    "function execute(string \$name) : string
    {
        echo \"I was generated!\";
    }";

        $this->assertSame($expectedfunction, $function->render());
    }
}