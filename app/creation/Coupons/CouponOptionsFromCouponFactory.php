<?php

namespace CouponURLs\App\Creation\Coupons;

use CouponURLs\App\Components\Options\CouponOptionsComponent;
use CouponURLs\App\Creation\Coupons\Couponurls\Abilities\CreatableFromCouponUrl;
use CouponURLs\App\Creation\OptionsFromTemplateAndOptionableFactory;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\Original\Collections\MappedObject;
use CouponURLs\Original\Environment\Env;
use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Text\i;

class CouponOptionsFromCouponFactory implements CreatableFromCouponUrl 
{
    public function __construct(
        protected CouponOptionsComponent $couponOptionsComponent,
        protected OptionsFromTemplateAndOptionableFactory $couponOptionsFromTemplateFactory,
    ) {}
    
    public function createFromCoupon(Coupon $coupon): MappedObject
    {
        (object) $couponUrlData = _(get_post_meta($coupon->id()));
        (object) $optionsTemplate = _($couponUrlData->find(
            fn(array $templates, string $key) => i($key)->__is(Env::getWithPrefix('options'))
        ));

        return $this->couponOptionsFromTemplateFactory->create(
            $optionsTemplate->getValues()->first(),
            $this->couponOptionsComponent
        );
    } 
}