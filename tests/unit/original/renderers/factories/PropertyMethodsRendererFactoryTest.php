<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Factories;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Language\Classes\Property;
use CouponURLs\Original\Renderers\Classes\VisibilityRenderer;
use CouponURLs\Original\Renderers\Factories\PropertyMethodsRendererFactory;
use CouponURLs\Original\Renderers\Language\NewLineRenderer;
use CouponURLs\Original\Renderers\Language\TextRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class PropertyMethodsRendererFactoryTest extends WP_UnitTestCase
{
    
    
        

    public function setUp() : void
    {
        $this->property = $this->createMock(Property::class);
    }
    
    public function test_getter_returns_a_visibility_renderer_when_a_property_is_public()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('public');

        (object) $renderer = $propertyMethodsRendererFactory->getGetterRenderer();

        $this->positiveAssertions($renderer);
    }

    public function test_getter_returns_a_visibility_renderer_when_a_property_is_readonly()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('public');

        (object) $renderer = $propertyMethodsRendererFactory->getGetterRenderer();

        $this->positiveAssertions($renderer);
    }

    public function test_getter_returns_a_text_renderer_when_a_property_is_private()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('private');

        (object) $renderer = $propertyMethodsRendererFactory->getGetterRenderer();

        $this->negativeAssertions($renderer);
    }

    protected function positiveAssertions($renderer)
    {
        $this->assertNotInstanceOf(TextRenderer::class, $renderer);
        $this->assertInstanceOf(NewLineRenderer::class, $renderer);   
    }

    protected function negativeAssertions($renderer)
    {
        $this->assertInstanceOf(TextRenderer::class, $renderer);
        $this->assertNotInstanceOf(NewLineRenderer::class, $renderer);   
    }

    public function test_setter_returns_a_visibility_renderer_when_a_property_is_public()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('public');

        (object) $renderer = $propertyMethodsRendererFactory->getSetterRenderer();

        $this->positiveAssertions($renderer);
    }

    public function test_setter_returns_a_text_renderer_when_a_property_is_readonly()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('readonly');

        (object) $renderer = $propertyMethodsRendererFactory->getSetterRenderer();

        $this->negativeAssertions($renderer);
    }

    public function test_setter_returns_a_text_renderer_when_a_property_is_private()
    {
        (object) $propertyMethodsRendererFactory = new PropertyMethodsRendererFactory($this->property);

        $this->property->expects($this->once())
                 ->method('getVisibility')
                 ->willReturn('private');

        (object) $renderer = $propertyMethodsRendererFactory->getSetterRenderer();

        $this->negativeAssertions($renderer);
    }
    
}