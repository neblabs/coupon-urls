<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneCorrectArgumentAndAnotherNoTypeHintFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $correctArgument,
        protected $iAmNotTyped
    ) {}
}