<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Domain;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Domain\Entity;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class EntityTest extends WP_UnitTestCase
{
    

    public function test_()
    {

    }
    // this test was an older version
    public function _test_field_is_set_properly_even_when_extending_class_has_no_constructor()
    {
        (object) $email = new class extends Entity {
            protected $id = FIELD_INTEGER::class;
        };

        $this->assertTrue(is_int($email->id));
    }

    // test that other properties in the class are not transformmed and there visiblity is intact (eg: we'll define a custom private property and we want to keep it that way.)
}