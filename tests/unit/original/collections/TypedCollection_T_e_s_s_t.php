<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Collections\TypedCollection;
use CouponURLs\Original\Utilities\UnexpectedTypeException;

Class _TypedCollectionTes_t extends \WP_UnitTestcase
{
    public function test_throws_exception_when_not_given_the_specified_type()
    {
        $this->expectException(UnexpectedTypeException::class);

        (array) $elements = [new \DateTime, true, [], new \DateTime];

        (object) $dirty = new Class($elements) extends TypedCollection {
            const TYPE = \DateTime::class;
        };
    }

    public function test_creates_a_new_object_when_specified_type_has_been_passed()
    {
        (array) $elements = [
            new \DateTime, 
            new \DateTime
        ];

        (object) $clean = new Class($elements) extends TypedCollection {
            const TYPE = \DateTime::class;
        };

        $this->assertInstanceof(Collection::class, $clean);
        $this->assertSame($elements, $clean->asArray());
    }
}