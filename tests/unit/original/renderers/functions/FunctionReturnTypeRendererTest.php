<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Functions;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Functions\FunctionReturnTypeRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class FunctionReturnTypeRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_returns_string_return_type()
    {
        (object) $functionReturnType = new FunctionReturnTypeRenderer;

        $functionReturnType->setType('string');

        $this->assertSame(': string', $functionReturnType->render());
    }

    public function test_returns_class_return_type()
    {
        (object) $functionReturnType = new FunctionReturnTypeRenderer;

        $functionReturnType->setType('Validator');

        $this->assertSame(': Validator', $functionReturnType->render());
    }

    public function test_returns_nothing_since_it_has_no_type_set()
    {
        (object) $functionReturnType = new FunctionReturnTypeRenderer;

        $this->assertSame('', $functionReturnType->render());
    }
}