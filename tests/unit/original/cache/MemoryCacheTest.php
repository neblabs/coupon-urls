<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Presentation\AttributesManager;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Cache\ExistingValueResolver;
use CouponURLs\Original\Cache\MemoryCache;
use CouponURLs\Original\Characters\StringManager;

Class MemoryCacheTest extends \WP_UnitTestcase
{
    /**
    * @dataProvider getProvider
    */
    public function test_gets_value(Array $data, $keyToGet, $expectedValue)
    {
        (object) $memoryCache = new MemoryCache($data);   

        $this->assertSame($expectedValue, $memoryCache->get($keyToGet));
    }

    /**
    * @dataProvider getifExistsProvider
    */
    public function test_returns_value_resolver_object(Array $data, $keyToGet, $expectedObject)
    {
        (object) $memoryCache = new MemoryCache($data);

        (object) $getifExistsReturnedValue = $memoryCache->getIfExists($keyToGet);

        $this->assertInstanceOf(ExistingValueResolver::class, $getifExistsReturnedValue);
        $this->assertEquals($expectedObject, $getifExistsReturnedValue);
    }
    
    
    public static function getProvider()
    {
       return [
           'value exists' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = 'user',
               $expectedValue = 'rafael'
           ],
           'value exists string manager' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = new StringManager('user'),
               $expectedValue = 'rafael'
           ],
           'value does not exist' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = 'city',
               $expectedValue = null
           ],
           'value does not exist string manager' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = new StringManager('city'),
               $expectedValue = null
           ]
       ];   
    }

    public function getifExistsProvider()
    {
       return [
           'value exists' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = 'user',
               $expectedObject = new ExistingValueResolver([
                    'key' => 'user',
                    'data' => new Collection([
                        'user' => 'rafael'
                    ])
                ]) 
           ],
           'value exists string manager' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = new StringManager('user'),
               $expectedObject = new ExistingValueResolver([
                    'key' => 'user',
                    'data' => new Collection([
                        'user' => 'rafael'
                    ])
                ])
           ],
           'value does not exist' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = 'city',
               $expectedObject = new ExistingValueResolver([
                    'key' => 'city',
                    'data' => new Collection([
                        'user' => 'rafael'
                    ])
                ])
           ],
           'value does not exist string manager' => [
               $data = [
                   'user' => 'rafael'
               ],
               $keyToGet = new StringManager('city'),
               $expectedObject = new ExistingValueResolver([
                    'key' => 'city',
                    'data' => new Collection([
                        'user' => 'rafael'
                    ])
                ])
           ]
       ];   
    }         
}