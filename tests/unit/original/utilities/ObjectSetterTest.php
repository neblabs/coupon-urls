<?php

namespace CouponURLs\tests\original\Utilities;

use CouponURLs\Original\Utilities\ObjectSetter;
use CouponURLs\tests\unit\original\utilities\doubles\ClassWithPrivateAndProtectedProperties;

Class ObjectSetterTest extends \WP_UnitTestCase
{
    public function test_sets_public_properties()
    {
        (object) $newobject = new \StdClass;
        (array) $values = [
            'name' => 'rafael',
            'id' => 9
        ];

        ObjectSetter::setPublicValues([
            'object' => $newobject,
            'values' => $values
        ]);   

        $this->assertTrue(isset($newobject->name));
        $this->assertTrue(isset($newobject->id));

        $this->assertSame($values, (array) $newobject);
    }

    public function test_halts_on_non_public_property_set_attempt()
    {
        $this->expectException(\Error::class);
        
        (object) $newobject = new ClassWithPrivateAndProtectedProperties;
        (array) $values = [
            'aProtectedProperty' => 'rafael',
        ];

        ObjectSetter::setPublicValues([
            'object' => $newobject,
            'values' => $values
        ]);   
    }
    
}