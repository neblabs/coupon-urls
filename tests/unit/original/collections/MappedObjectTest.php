<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\MappedObject;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Characters\StringManager;

Class MappedObjectTest extends \WP_UnitTestcase
{
    public function test_returns_publicly_set_fields()
    {
        (object) $mappedObject = new MappedObject;   

        $mappedObject->name = 'rafael';
        $mappedObject->id = 0;

        (array) $expectedFields = [
            'name' => 'rafael',
            'id' => 0
        ];

        $this->assertSame($expectedFields, $mappedObject->asArray()); 
    }

    public function test_returns_publicly_set_fields_recursive()
    {
        (object) $mappedObject = new MappedObject;   

        $mappedObject->name = 'rafael';
        $mappedObject->id = 0;
        $mappedObject->users = new MappedObject([
            'name' => 'all'
        ]);

        (array) $expectedFields = [
            'name' => 'rafael',
            'id' => 0,
            'users' => [
                'name' => 'all'
            ]
        ];

        $this->assertSame($expectedFields, $mappedObject->asArray()); 
    }
 
    public function test_instatiates_with_initial_values()
    {
        (array) $data = [
            'name' => 'rafael',
            'id' => 7
        ];
        
        (object) $mappedObject = new MappedObject($data);

        $this->assertTrue(isset($mappedObject->name));
        $this->assertTrue(isset($mappedObject->id));

        $this->assertSame('rafael', $mappedObject->name);
        $this->assertSame(7, $mappedObject->id);

        $this->assertSame($data, $mappedObject->asArray());
    }

    public function test_returns_collection_of_public_values()
    {
        
        (object) $mappedObject = new MappedObject;

        $mappedObject->name = 'rafael';
        $mappedObject->id = 7;

        $this->assertSame('rafael', $mappedObject->name);
        $this->assertSame(7, $mappedObject->id);

        (object) $collection = new Collection([
            'name' => 'rafael',
            'id' => 7
        ]);

        $this->assertInstanceOf(Collection::class, $mappedObject->asCollection());
        $this->assertEquals($collection, $mappedObject->asCollection());
    }

    public function test_clones_all_its_object_when_it_is_cloned()
    {
        (object) $mappedObject = new MappedObject;
        (object) $nameObject = new \StdClass;

        $mappedObject->name = $nameObject; 

        (object) $clonedMappedObject = clone $mappedObject;

        $this->assertNotSame($mappedObject, $clonedMappedObject);
        $this->assertNotSame($nameObject, $clonedMappedObject->name);
    }
    
          
}