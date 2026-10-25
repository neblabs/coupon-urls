<?php declare(strict_types=1);

use CouponURLs\App\Creation\Actions\ActionsFactory;
use CouponURLs\App\Domain\Actions\CouponAdders\AddCoupon;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Actions\Messages\CouponAddedToCartExtraNotificationMessage;
use CouponURLs\App\Domain\Actions\Messages\CouponToBeAddedNotificationMessage;
use CouponURLs\App\Domain\Actions\Products\AddProduct;
use CouponURLs\App\Domain\Actions\Redirections\Redirection;
use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Carts\CartItem;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;
use CouponURLs\App\Domain\Products\Product;
use CouponURLs\App\Domain\Redirections\PostTypeRedirectionValidator;
use CouponURLs\App\Domain\Redirections\PostTypeURL;
use CouponURLs\App\Domain\Redirections\RelativeURL;
use CouponURLs\App\Domain\Redirections\WordPressURLRedirector;
use CouponURLs\Original\System\NativeExiter;
use CouponURLs\Original\Validation\Validators\PassingValidator;

use function CouponURLs\Original\Utilities\Collection\{a, _};

beforeEach(function() {
    expect(wc()->cart->get_applied_coupons())->toBe([]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe(0);
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
it('creates the six actions from coupon meta data in the database', function() {
    $product = $this::class::factory()->post->create_and_get(a(
        post_type: 'product',
        post_title: 'iPod Touch',
        //meta
        meta_input: a(
            _price: 100,
            _regular_price: 100
        )
    ));
    $secondProduct = $this::class::factory()->post->create_and_get(a(
        post_type: 'product',
        post_title: 'iPod Touch Red',
        //meta
        meta_input: a(
            _price: 100,
            _regular_price: 100
        )
    ));
    $couponPost = $this::class::factory()->post->create_and_get(a(
        post_type: 'shop_coupon',
        post_title: 'FREE500',
        //meta
        meta_input: a(
            discount_type: 'percent',
            coupon_amount: 50,
            individual_use: 'no',
            coupon_urls_action_AddCoupon: '',
            coupon_urls_action_AddProduct: _(
                id: $product->ID,
                quantity: 3
            )->asJson()->get(),
            coupon_urls_action_Redirection: _(
                type: 'postType',
                value: 4
            )->asJson()->get(),
            coupon_urls_action_CouponToBeAddedNotificationMessage: _(
                message: 'Your cupon hasnt been added yet!'
            )->asJson()->get(),
            coupon_urls_action_CouponAddedToCartExtraNotificationMessage: _(
                message: 'Your shiny coupon was added!'
            )->asJson()->get(),
        )
    ));


    /** @var ActionsFactory */
    (object) $actionsFactory = apply_filters('ActionsFactory', null);
    /** @var Cart */
    (object) $cart = apply_filters('Cart', null);
    /** @var CouponsToBeAdded */
    (object) $couponsToBeAdded = apply_filters('CouponsToBeAdded', null);
    (object) $coupon = new Coupon(new WC_Coupon($couponPost->ID), new WC_Discounts(wc()->cart));


    expect($actionsFactory->createFromCoupon($coupon)->asArray())->toEqual(a(
        AddCoupon: new AddCoupon(_(
            new CouponCartAdder($coupon, $cart),
            new CouponToBeAddedCartAdder($coupon, $couponsToBeAdded, $cart)
        )),
        AddProduct: new AddProduct(
            item: new CartItem(
                product: new Product(new WC_Product_Simple($product->ID)),
                quantity: 3
            ),
            coupon: $coupon,
            cart: $cart
        ),
        Redirection: new Redirection(
            new WordPressURLRedirector(
                url: new PostTypeURL(4),
                redirectionValidator: new PostTypeRedirectionValidator(4),
                exiter: new NativeExiter,
            )
        ),
        CouponToBeAddedNotificationMessage: new CouponToBeAddedNotificationMessage(
            message: 'Your cupon hasnt been added yet!',
            couponsToBeAdded: $couponsToBeAdded,
            coupon: $coupon
        ),
        CouponAddedToCartExtraNotificationMessage: new CouponAddedToCartExtraNotificationMessage(
            message: 'Your shiny coupon was added!',
            cart: $cart,
            coupon: $coupon
        )
    ));
});
