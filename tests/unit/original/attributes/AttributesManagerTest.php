<?php

namespace CouponURLs\tests\original\collections;

use CouponURLs\Original\Presentation\AttributesManager;
use CouponURLs\Original\Tests\DatabaseTestCase;

Class AttributesManagerTest extends \WP_UnitTestcase
{
    public function test_returns_attributes_as_html()
    {
        (object) $attributesManager = new AttributesManager;

        (string) $builtAttribute = $attributesManager->build([
            'name' => 'title',
            'value' => 'title value'
        ]);

        $this->assertEquals(' title="title value"', $builtAttribute);   
    }

    public function test_escapes_malicious_output()
    {
        (object) $attributesManager = new AttributesManager;

        (string) $builtAttribute = $attributesManager->build([
            'name' => 'title',
            'value' => '" onclick="gotcha()"'
        ]);

        $this->assertEquals(' title="&quot; onclick=&quot;gotcha()&quot;"', $builtAttribute);   
    }
}