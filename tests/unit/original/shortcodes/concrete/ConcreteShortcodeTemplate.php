<?php

namespace CouponURLs\tests\unit\original\shortcodes\concrete;

use CouponURLs\Original\Shortcodes\Shortcode;
use CouponURLs\Original\Collections\Mapper\Types;

Class ConcreteShortcodeTemplate extends Shortcode 
{
    protected static $name = 'concrete_shortcode';

    protected function map()
    {
        return [
            'property' => Types::STRING,
            'defaultProperty' => Types::STRING()->withDefault('default property with default value')
        ];
    }

    public function render()
    {

    }
}