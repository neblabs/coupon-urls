<?php declare(strict_types=1);

namespace CouponURLs\Tests\Unit\Original\Renderers\Language;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Renderers\Language\VariableRenderer;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class VariableRendererTest extends WP_UnitTestCase
{
    
    
        

    public function test_rendres_a_variable_name_no_values_and_no_semi_colon()
    {
        (object) $variableRenderer = new VariableRenderer('myVariable');

        $this->assertSame('$myVariable', $variableRenderer->render());
    }
}