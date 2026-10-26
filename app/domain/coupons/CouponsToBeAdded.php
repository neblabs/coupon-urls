<?php

 namespace CouponURLs\App\Domain\Coupons;

use CouponURLs\App\Creation\Coupons\CouponsFactory;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\Coupons;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Environment\Env;
use WC_Session_Handler;

use function CouponURLs\Original\Utilities\Collection\_;

class CouponsToBeAdded
 {
    #The coupons added to the current instance by calling static::add()
    protected Collection /*<string>*/ $couponCodesAddedToQueue;

    public function __construct(
        protected WC_Session_Handler $session,
        protected CouponsFactory $couponsFactory,
        ?Collection $couponCodes = null
    ) {
        if ($couponCodes || !$this->session->get($this->key())) {
            $this->setCouponCodes($couponCodes ?? _());
        }

        $this->couponCodesAddedToQueue = _();
    }

    public function coupons() : Coupons
    {
        return $this->couponsFactory->createFromCodes($this->couponCodes());
    }

    /**
     * READONLY COPY, MODIFYING THIS DIRECTLY WILL HAVE NO EFFECT!
     */
    public function couponCodes() : Collection
    {
        return _($this->session->get($this->key()));
    }

    public function has(Coupon $coupon) : bool
    {
        return $this->couponCodes()->have($coupon->code());
    }

    public function hasAddedToTheQueue(Coupon $coupon) : bool
    {
        return $this->couponCodesAddedToQueue->have($coupon->code());
    }

    public function add(Coupon $coupon) : void
    {
        $this->couponCodesAddedToQueue->push($coupon->code());

        $this->setCouponCodes(
            $this->couponCodes()->push($coupon->code()->get())->withoutDuplicates()
        );
    }

    public function remove(Coupon $coupon) : void
    {
        $this->setCouponCodes(
            $this->couponCodes()->filter(
                fn(string $couponCode) => !$coupon->code()->__is($couponCode)
            )->getValues()
        );
    }

    protected function setCouponCodes(Collection $couponCodes) : void
    {
        if ($couponCodes->filter()->haveAny() && !$this->session->has_session()) {
            $this->session->set_customer_session_cookie(true);
        }
        
        $this->session->set($this->key(), $couponCodes->filter()->asArray());

        $this->session->save_data();
    }
    
    protected function key() : string
    {
        return Env::getWithPrefix('coupons_to_be_added');
    }
}