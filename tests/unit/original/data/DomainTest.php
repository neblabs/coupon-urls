<?php

namespace CouponURLs\tests\unit\original\data;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Data\Model\Domain;
use CouponURLs\Original\Characters\StringManager;
use CouponURLs\tests\unit\original\data\doubles\DomainDoubleWithMap;

Class DomainTest extends \WP_UnitTestCase
{
    public function test_builds_map_if_class_has_map_method()
    {
        (object) $domain = new DomainDoubleWithMap(['id' => 5, 'date' => new StringManager('overriden date!')]);

        $this->assertTrue(isset($domain->id));
        $this->assertIsInt($domain->id);
        $this->assertEquals(5, $domain->id);

        $this->assertTrue(isset($domain->name));
        $this->assertInstanceOf(StringManager::class, $domain->name);
        $this->assertEquals('the default name', $domain->name);

        $this->assertTrue(isset($domain->name));
        $this->assertInstanceOf(StringManager::class, $domain->date);
        $this->assertEquals('overriden date!', $domain->date);
    }

    public function test_builds_domain_from_json()
    {
        (object) $domain = DomainDoubleWithMap::fromJson('{"date": "overriden date!"}');

        $this->assertTrue(isset($domain->id));
        $this->assertIsInt($domain->id);

        $this->assertTrue(isset($domain->name));
        $this->assertInstanceOf(StringManager::class, $domain->name);
        $this->assertEquals('the default name', $domain->name);

        $this->assertTrue(isset($domain->name));
        $this->assertInstanceOf(StringManager::class, $domain->date);
        $this->assertEquals('overriden date!', $domain->date);
    }

    public function test_gets_scalar_available_values()
    {
        (object) $domain = new DomainDoubleWithMap(['name' => 'new name']);

        (array) $exepectedValues = [
            'id' => 0,
            'name' => 'new name',
            'date' => 'default date'
        ];

        $this->assertSame($exepectedValues, $domain->getAvailableValues());
    }
}