<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;
use stdClass;

class ProductDependencyRequiresCustomerFake implements Cached, Dependency, StaticType
{
    public function __construct(
        protected CustomerFake $customerFake
    ) {}
    
    static public function type(): string
    {
        return ProductFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return new ProductFake(customer: $this->customerFake);
    } 
}