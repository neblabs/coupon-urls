<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\WillAlwaysMatch;

class AccountRequiresNothingFake implements Cached, Dependency, StaticType
{
    use WillAlwaysMatch;
    
    static public function type(): string
    {
        return AccountFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return new AccountFake(id: 10);
    } 
}