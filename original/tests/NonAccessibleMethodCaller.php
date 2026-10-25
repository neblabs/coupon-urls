<?php

namespace CouponURLs\Original\Tests;

class NonAccessibleMethodCaller
{
  public static function call($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}