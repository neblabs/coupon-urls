<?php

namespace CouponURLs\App\Domain\Products;

use WC_Product;
use WC_Product_Variable;
use WC_Product_Variation;

use function CouponURLs\Original\Utilities\Text\i;

class Product
{
    public function __construct(
        public WC_Product|WC_Product_Variation|WC_Product_Variable $classic
    ) {}
    
    public function is(string|Product $typeOrProduct) : bool
    {
        return match(true) {
            $typeOrProduct instanceof Product => $this->checkIsProduct($typeOrProduct),
            default => i($this->classic->get_type())->__is($typeOrProduct)
        };
    }

    protected function checkIsProduct(Product $product) : bool
    {
        return $product->classic->get_id() === $this->classic->get_id() &&
               $product->classic->get_parent_id() === $this->classic->get_parent_id();

    }
    
}