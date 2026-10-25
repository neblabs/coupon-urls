<?php declare(strict_types=1);

namespace Tests\Unit\Original\Environment;

use CouponURLs\App\Tests\CouponComponents;
use CouponURLs\App\Tests\FilterAssertions;
use CouponURLs\App\Tests\OfferAssertions;
use CouponURLs\App\Tests\ProductsDatabaseLoader;
use CouponURLs\Original\Environment\Env;
use CouponURLs\Original\Tests\DataProviderLoader;
use CouponURLs\Original\Tests\DatabaseTestCase;
use WP_UnitTestCase;

Class EnvTest extends WP_UnitTestCase
{
    
    
        

    public function test_adds_base_namsepace_to_a_relative_namespace()
    {
        (string) $relativeNamespace = 'App\\Model\\Options';

        $this->assertSame('CouponURLs\\App\\Model\\Options', Env::getwithBaseNamespace($relativeNamespace));
    }
    
    public function test_converts_directory_path_to_namespace()
    {
        $this->assertSame('CouponURLs\\App\\Model\\Options', Env::getNamespaceFromDirectory('/app/model/options'));
    }
}