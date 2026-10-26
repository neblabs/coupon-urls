<?php

namespace CouponURLs\App\Domain\Carts;

use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Products\Product;
use CouponURLs\Original\Collections\Collection;
use CouponURLs\Original\Domain\Entity;
use WC_Cart;
use WC_Product;

use function CouponURLs\Original\Utilities\Collection\_;

Class Cart extends Entity
{
    protected Collection /*<Coupon>*/ $couponsAdded;

    public function __construct(
        public readonly WC_Cart $classic
    ) {
        $this->couponsAdded = _();
    }

    public function isEmpty() : bool
    {
        return !$this->classic->get_cart_contents();
    }

    public function has(Coupon|CartItem $couponOrItem) : bool
    {
        return match(true) {
            $couponOrItem instanceof Coupon => $this->hasCoupon($couponOrItem),
            $couponOrItem instanceof CartItem => $this->hasItem($couponOrItem)
        };
    }

    public function hasProduct(Product $product) : bool
    {
        return _($this->classic->get_cart_contents())
            ->filter(fn(array $item) => $item['data'] instanceof WC_Product)
            ->map(fn(array $item) : Product => new Product($item['data']))
            ->findTheOneThat(is: $product);
    }

    public function quanititiesOf(Product $product) : int
    {
        return _($this->classic->get_cart_contents())
            ->filter(fn(array $item) => $item['data'] instanceof WC_Product)
            ->filter(fn(array $item) => (new Product($item['data']))->is($product))
            ->mapUsing(quantity: null)
            ->sum();
    }

    public function add(Coupon|CartItem $couponOrItem) : void
    {
        match(true) {
            $couponOrItem instanceof Coupon => $this->addCoupon($couponOrItem),
            $couponOrItem instanceof CartItem => $this->addItem($couponOrItem)
        };
    }

    public function hasAddedCoupon(Coupon $coupon) : bool
    {
        return $this->couponsSuccessfullyAdded()->strictlyHave($coupon);
    }

    public function generateItemKey(CartItem $cartItem) : string
    {
        return $this->classic->generate_cart_id(
            ...$cartItem->export()->except(['quantity'])->getValues()->asArray()
        );
    }

    protected function addCoupon(Coupon $coupon) : void
    {
        if ($this->has($coupon)) {
            return;
        }
        
        if($this->classic->apply_coupon($coupon->code()->get())) {
            $this->couponsAdded->push($coupon);
        }
    }

    protected function addItem(CartItem $cartItem) : void
    {
        $this->classic->add_to_cart(...$cartItem->export()->getValues()->asArray());
    }

    public function hasCoupon(Coupon $coupon) : bool
    {
        return _($this->classic->get_applied_coupons())->have(
            fn(string $couponCode) => $coupon->code()->__is($couponCode)
        );
    }

    public function hasItem(CartItem $item) : bool
    {
        return (!$this->isEmpty() && $this->classic->find_product_in_cart($item->key($this)));
    }

    // this will only give you the coupons that we added AND that were succesfully 
    // added to the cart
    protected function couponsSuccessfullyAdded() : Collection
    {
        return $this->couponsAdded->filter($this->has(...));
    }    
}