<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Abilities\Renderable;
use CouponURLs\Original\Renderers\Language\ImportRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;
use stdClass;

Class ImportRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_creates_an_impor_of_a_namespaced_class()
    {
        (object) $import = new ImportRenderer(Renderable::class);

        $this->assertSame('use '.Renderable::class, $import->render());
    }

    public function test_creates_an_impor_of_a_global_class()
    {
        (object) $import = new ImportRenderer(stdClass::class);

        $this->assertSame('use '.stdClass::class, $import->render());
    }
}