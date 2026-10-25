<?php

namespace CouponURLs\tests\original\shortcodes;

use CouponURLs\Original\Environment\Env;
use CouponURLs\Original\Shortcodes\Shortcode;
use CouponURLs\tests\unit\original\shortcodes\concrete\ConcreteShortcodeTemplate;

Class ShortcodeTest extends \WP_UnitTestCase
{
    public function test_returns_name_with_id()
    {
        (object) $shortcode = new ConcreteShortcodeTemplate(
            $args = [], 
            $name = ''
        );

        $this->assertEquals(Env::idLowercase() . '_concrete_shortcode', $shortcode::name());
    }

    public function test_maps_arguments_to_properties()
    {
        (object) $shortcode = new ConcreteShortcodeTemplate(
            $args = ['property' => 'with value'], 
            $name = ''
        );

        $this->assertInstanceof(\StdClass::class, $shortcode->getProperties());
        $this->assertCount(2, get_object_vars($shortcode->getProperties()));

        
        
        $this->assertTrue(isset($shortcode->getProperties()->property));
        $this->assertEquals('with value', $shortcode->getProperties()->property);

        $this->assertTrue(isset($shortcode->getProperties()->defaultProperty));
        $this->assertEquals('default property with default value', $shortcode->getProperties()->defaultProperty);
    }
}