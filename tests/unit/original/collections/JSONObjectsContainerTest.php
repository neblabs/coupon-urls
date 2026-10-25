<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\tests\unit\original\collections\Implementations\JSONObjectsContainerDouble;

Class JSONObjectsContainerTest extends \WP_UnitTestcase
{
    public function test_get_objects()
    {
        $this->assertinstanceOf(Collection::class, JSONObjectsContainerDouble::getObjects());
        $this->assertContainsOnlyInstancesOf(Collection::class, JSONObjectsContainerDouble::getObjects()->asArray());
    }

    public function test_returns_object()
    {
        $result = JSONObjectsContainerDouble::get('theValue');

        $this->assertinstanceOf(Collection::class, $result);
        $this->assertEquals([
                'theKey' => 'theValue'
            ], $result->asArray());
    }

    public function test_returns_null_not_found_object()
    {
        $result = JSONObjectsContainerDouble::get('non value');

        $this->assertEquals(null, $result);
    }
}