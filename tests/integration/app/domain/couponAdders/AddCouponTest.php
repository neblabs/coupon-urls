<?php declare(strict_types=1);

use CouponURLs\App\Creation\Coupons\CouponFactory;
use CouponURLs\App\Creation\Coupons\CouponsFactory;
use CouponURLs\App\Domain\Actions\ActionsComposite;
use CouponURLs\App\Domain\Actions\ActionsRunnerComposite;
use CouponURLs\App\Domain\Actions\CouponAdders\AddCoupon;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Actions\NonRestrictableActionRunner;
use CouponURLs\App\Domain\Actions\RestrictableActionRunner;
use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;

use function CouponURLs\Original\Utilities\Collection\_;
use function CouponURLs\Original\Utilities\Collection\a;

beforeEach(function() {
    expect(wc()->cart->get_applied_coupons())->toBe([]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe(0);
    $this->couponsFactory = new CouponsFactory(new CouponFactory(new WC_Discounts(wc()->cart)));
});

afterEach(function() : void {
    wp_cache_flush();

    \WC()->cart->empty_cart($force = true);
    \WC()->cart->remove_coupons();
    \WC()->session->set('chosen_payment_method', null);
    \WC()->session->delete_session(\WC()->session->get_customer_unique_id());
    \WC()->cart->shipping_total = 0;
    \WC()->session->set( 'cart', null );
    \WC()->session->set( 'cart_totals', null );
    \WC()->session->set( 'applied_coupons', null );
    \WC()->session->set( 'coupon_discount_totals', null );
    \WC()->session->set( 'coupon_discount_tax_totals', null );
    \WC()->session->set( 'removed_cart_contents', null );
    \WC()->session->set( 'order_awaiting_payment', null );
});
/** @var UnitTestCase  */
it('scenario: no other coupons and coupon to add passes options', function() {
    //save the products in them database
    $product = $this::class::factory()->post->create_and_get(a(
        post_type: 'product',
        post_title: 'iPod Touch',
        //meta
        meta_input: a(
            _price: 100,
            _regular_price: 100
        )
    ));
    $cpres = $this::class::factory()->post->create_and_get(a(
        post_type: 'shop_coupon',
        post_title: 'FREE500',
        //meta
        meta_input: a(
            discount_type: 'percent',
            coupon_amount: 50,
            individual_use: 'no'
        )
    ));

    //load them in the cart
    wc()->cart->add_to_cart(
        product_id: $product->ID  
    );
    //run the actions
    (object) $cart = new Cart(wc()->cart);
    (object) $couponsToBeAdded = new CouponsToBeAdded(wc()->session, $this->couponsFactory);
    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddCoupon(_(
                new CouponCartAdder($coupon, $cart),
                new CouponToBeAddedCartAdder($coupon, $couponsToBeAdded, $cart)
            ))
        ),
        actionsRunner: new ActionsRunnerComposite(
            actionRunners: _(
                new RestrictableActionRunner,
                new NonRestrictableActionRunner
            )
        )
    );

    $actions->perform();
    //the check the cart to see the result of them actions!
    //expect that the coupon was added and that the cart now has a 50% discount!
    expect(wc()->cart->get_applied_coupons())->toBe(['free500']);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('50.00');
});

/** @var UnitTestCase  */
it('doesn"t meet the requirements so its added to a ToBeAdded, the requirements then change and its now added', function() {
    //save the products in them database
    $product = $this::class::factory()->post->create_and_get(a(
        post_type: 'product',
        post_title: 'iPod Touch',
        //meta
        meta_input: a(
            _price: 60,
            _regular_price: 60
        )
    ));
    $cpres = $this::class::factory()->post->create_and_get(a(
        post_type: 'shop_coupon',
        post_title: 'FREE500',
        //meta
        meta_input: a(
            discount_type: 'percent',
            coupon_amount: 50,
            minimum_amount: 100,
            individual_use: 'no'
        )
    ));

    //load them in the cart
    wc()->cart->add_to_cart(
        product_id: $product->ID  
    );
    //run the actions
    (object) $cart = new Cart(wc()->cart);
    (object) $couponsToBeAdded = new CouponsToBeAdded(wc()->session, $this->couponsFactory);
    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddCoupon(_(
                new CouponCartAdder($coupon, $cart),
                new CouponToBeAddedCartAdder($coupon, $couponsToBeAdded, $cart)
            ))
        ),
        actionsRunner: new ActionsRunnerComposite(
            actionRunners: _(
                new RestrictableActionRunner,
                new NonRestrictableActionRunner
            )
        )
    );

    $actions->perform();
    //the check the cart to see the result of them actions!
    //expect that the coupon was added and that the cart now has a 50% discount!
    expect(wc()->cart->get_applied_coupons())->toBe([]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('60.00');

    ob_start();
    woocommerce_cart_totals();
    $cartTable = ob_get_clean();

    expect($cartTable)->toContain('<td class="coupon_urls_to-be-applied-code">free500</td>');

    wc()->cart->add_to_cart(product_id: $product->ID, quantity: 2 /*should have 3 in total*/);

    expect(wc()->cart->get_applied_coupons())->toBe(['free500']);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('90.00' /*3*60/50%*/);

    ob_start();
    woocommerce_cart_totals();
    $cartTable = ob_get_clean();
    expect($cartTable)->not->toContain('<td class="coupon_urls_to-be-applied-code">free500</td>');
});
//add a simple coupon:
//     sceanrios: 
//          -no other coupons and -coupon passes options: (both with an empty cart and cart with prods)
//              -coupon has no conditions
//              -coupon has a minimum spend and it is met
//          -no other coupons and -coupon doesnt pass
//              -coupon has a minimum spend not met
//              -coupon has a minimum spend not met (then it is met so it gets applied!)
//          -with other coupons and -coupon passes options:
//              -coupon has no conditions
//              -coupon has a minimum spend and it is met
//          -with other coupons and -coupon doesnt pass
//              -the other, already applied coupin cant be convined
//              -the other, already applied coupin cant be convined (then the other gets removed so the new one gets applied!)
//              -coupon has a minimum spend not met (then it is met so it gets applied!)
//          -with the same coupon already applied
//              -coupon has no conditions

