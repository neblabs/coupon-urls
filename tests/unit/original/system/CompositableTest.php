<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\System;

use ArrayAccess;
use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\System\Compositable;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use JsonSerializable;
use WP_UnitTestCase;

Class CompositableTest extends WP_UnitTestCase
{
    
    
        

    private $jsonSerializables;
    private $arrayAccesses;

    public function setUp() : void
    {
        $this->jsonSerializables = [
            $this->createMock(JsonSerializable::class),
            $this->createMock(JsonSerializable::class),
            $this->createMock(JsonSerializable::class),
        ];

        $this->arrayAccesses = [
            $this->createMock(ArrayAccess::class),
            $this->createMock(ArrayAccess::class),
            $this->createMock(ArrayAccess::class),
        ];
    }
    
    public function test_the_specified_method_is_executed_on_all_passed_objects_and_no_given_arguments_array_passed()
    {
        (object) $compositable = new Compositable($this->jsonSerializables);

        foreach ($this->jsonSerializables as $jsonSerializable) {
            $jsonSerializable->expects($this->once())
                             ->method('jsonSerialize');
        }

        $compositable->execute('jsonSerialize');
    }

    public function test_the_specified_method_is_executed_on_all_passed_objects_and_no_given_arguments_collection_passed()
    {
        (object) $compositable = new Compositable(new Collection($this->jsonSerializables));

        foreach ($this->jsonSerializables as $jsonSerializable) {
            $jsonSerializable->expects($this->once())
                             ->method('jsonSerialize');
        }

        $compositable->execute('jsonSerialize');
    }

    public function test_method_is_executed_using_magin_methods()
    {
        (object) $compositable = new Compositable($this->jsonSerializables);

        foreach ($this->jsonSerializables as $jsonSerializable) {
            $jsonSerializable->expects($this->once())
                             ->method('jsonSerialize');
        }

        $compositable->jsonSerialize();
    }

    public function test_the_specified_method_is_executed_on_all_passed_objects_and_with_arguments()
    {
        (object) $compositable = new Compositable($this->arrayAccesses);

        (integer) $offset = 7;
        (string) $value = 'myvalue';

        foreach ($this->arrayAccesses as $arrayAccesses) {
            $arrayAccesses->expects($this->once())
                             ->method('offsetSet')
                             ->with($value, $offset);
        }

        $compositable->execute('offsetSet', [$value, $offset]);
    }

    public function test_the_specified_method_is_executed_on_all_passed_objects_and_with_arguments_suing_the_magic_method()
    {
        (object) $compositable = new Compositable($this->arrayAccesses);

        (integer) $offset = 7;
        (string) $value = 'myvalue';

        foreach ($this->arrayAccesses as $arrayAccesses) {
            $arrayAccesses->expects($this->once())
                             ->method('offsetSet')
                             ->with($value, $offset);
        }

        $compositable->offsetSet($value, $offset);
    }
}