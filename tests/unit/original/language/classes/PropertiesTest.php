<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Language\Classes;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Language\Classes\Properties;
use CouponURLs\Original\Language\Classes\Property;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;
use stdClass;

Class PropertiesTest extends WP_UnitTestCase
{
    
    
        

    public function test_returns_only_the_properties_type_hinted_by_classes()
    {
        (object) $stringProperty = $this->createMock(Property::class);
        (object) $untypedProperty = $this->createMock(Property::class);
        (object) $globalClassProperty = $this->createMock(Property::class);
        (object) $integerProperty = $this->createMock(Property::class);
        (object) $namespacedClassProperty = $this->createMock(Property::class);
        (object) $interfaceProperty = $this->createMock(Property::class);

        (object) $properties = new Properties([
            $stringProperty,
            $untypedProperty,
            $globalClassProperty,
            $integerProperty,
            $namespacedClassProperty,
            $interfaceProperty
        ]);

        $stringProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('string');
        
        $untypedProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('');

        $globalClassProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(stdClass::class);

        $integerProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('int');

        $namespacedClassProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(Properties::class);

        $interfaceProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(Renderable::class);

        (object) $result = $properties->getAllClassTyped();
        (array) $expectedResult = [
            $globalClassProperty,
            $namespacedClassProperty,
            $interfaceProperty
        ];
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($expectedResult, $result->getValues()->asArray());
    }

    public function _test_returns_only_the_properties_type_hinted_by_classes_only_one_without_duploicate_types()
    {
        (object) $stringProperty = $this->createMock(Property::class);
        (object) $untypedProperty = $this->createMock(Property::class);
        (object) $globalClassProperty = $this->createMock(Property::class);
        (object) $integerProperty = $this->createMock(Property::class);
        (object) $namespacedClassProperty = $this->createMock(Property::class);
        (object) $interfaceProperty = $this->createMock(Property::class);
        (object) $namespacedClassPropertyWithSameTypeAsAbove = $this->createMock(Property::class);

        (object) $properties = new Properties([
            $stringProperty,
            $untypedProperty,
            $globalClassProperty,
            $integerProperty,
            $namespacedClassProperty,
            $interfaceProperty,
            $namespacedClassPropertyWithSameTypeAsAbove
        ]);

        $stringProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('string');
        
        $untypedProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('');

        $globalClassProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(stdClass::class);

        $integerProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn('int');

        $namespacedClassProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(Properties::class);

        $interfaceProperty->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(Renderable::class);

        $namespacedClassPropertyWithSameTypeAsAbove->expects($this->once())
                       ->method('getShortType')
                       ->willReturn(Properties::class);

        (object) $result = $properties->getAllClassTyped();
        (array) $expectedResult = [
            $globalClassProperty,
            $namespacedClassProperty,
            $interfaceProperty
        ];
        $this->assertInstanceof(Collection::class, $result);
        $this->assertSame($expectedResult, $result->getValues()->asArray());
    }
}