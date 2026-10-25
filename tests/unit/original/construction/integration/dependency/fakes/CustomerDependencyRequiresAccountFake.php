<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;
use stdClass;

class CustomerDependencyRequiresAccountFake implements Cached, Dependency, StaticType
{
    public function __construct(
        protected AccountFake $accountFake
    ) {}
    
    static public function type(): string
    {
        return CustomerFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return new CustomerFake(account: $this->accountFake);
    } 
}