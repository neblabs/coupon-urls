<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Validation\Validators;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use CouponURLs\Original\Validation\Exceptions\InvalidTypeException;
use CouponURLs\Original\Validation\Exceptions\ValidationException;
use CouponURLs\Original\Validation\Validators\ValidType;
use Exception;
use PDO;
use Serializable;
use WP_UnitTestCase;
use stdClass;

Class ValidTypeTest extends WP_UnitTestCase
{
    public function test_passes_when_is_instance_of_the_same_class()
    {
        (object) $stdType = new ValidType(new stdClass, stdClass::class);

        $stdType->validate();

        $this->assertTrue($stdType->isValid());
    }

    public function test_passes_when_is_instance_of_the_child_class()
    {
        (object) $stdType = new ValidType(new class extends stdClass {

        }, stdClass::class);

        $stdType->validate();

        $this->assertTrue($stdType->isValid());
    }

    public function test_passes_when_is_instance_of_an_interface()
    {
        (object) $serializable = new ValidType(new class implements Serializable {
            public function serialize() {}
            public function unserialize($serialized) {}
            public function __serialize(): array
            {
                return [];   
            }

            public function __unserialize(array $data) : void
            {
                
            }
        }, Serializable::class);

        $serializable->validate();

        $this->assertTrue($serializable->isValid());
    }

    public function test_fails_when_its_another_instance()
    {
        $this->expectException(InvalidTypeException::class);

        (object) $stdType = new ValidType(new stdClass, PDO::class);

        $this->assertFalse($stdType->isValid());
        // needs to the at the bottom because after the exception is thrown no code will be executed.
        $stdType->validate();
    }
}