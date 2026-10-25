<?php declare(strict_types=1);

use CouponURLs\App\Creation\Coupons\CouponFactory;
use CouponURLs\App\Creation\Coupons\CouponsFactory;
use CouponURLs\App\Domain\Actions\ActionsComposite;
use CouponURLs\App\Domain\Actions\ActionsRunnerComposite;
use CouponURLs\App\Domain\Actions\CouponAdders\AddCoupon;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Actions\NonRestrictableActionRunner;
use CouponURLs\App\Domain\Actions\Products\AddProduct;
use CouponURLs\App\Domain\Actions\RestrictableActionRunner;
use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Carts\CartItem;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;
use CouponURLs\App\Domain\Products\Product;

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
it('applies a simple product, doesn"t exist in the cart', function() {
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

    //load them in the cart

    //run the actions
    (object) $cart = new Cart(wc()->cart);

    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddProduct(
                item: new CartItem(
                    product: new Product(wc_get_product($product->ID)),
                    quantity: 1
                ),
                coupon: $coupon,
                cart: $cart
            )
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

    expect(_(wc()->cart->cart_contents)->mapUsing(product_id: null))->toContain($product->ID);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('100.00');

    //LETS RUN THE ACTIONS AGAIN, MAKE SURE WE AIN'T ADDING THE PRODUCT AGAIN!
    $actions->perform();

    expect(_(wc()->cart->cart_contents)->mapUsing(product_id: null))->toContain($product->ID);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('100.00');
});

it('applies a simple product, already exists in the cart (NOT added by us)', function(int $existingProductQuantity, int $expectedQuanityToAdd, string $expectedCartTotal) {
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

    //load them in the cart
    wc()->cart->add_to_cart(
        product_id: $product->ID,
        quantity: $existingProductQuantity
    );
    //run the actions
    (object) $cart = new Cart(wc()->cart);

    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddProduct(
                item: new CartItem(
                    product: new Product(wc_get_product($product->ID)),
                    quantity: $expectedQuanityToAdd
                ),
                coupon: $coupon,
                cart: $cart
            )
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

    expect(_(wc()->cart->cart_contents)->mapUsing(product_id: null))->toContain($product->ID);
    expect(wc()->cart->get_total(context: 'edit'))->toBe($expectedCartTotal);

    //LETS RUN THE ACTIONS AGAIN, MAKE SURE WE AIN'T ADDING THE PRODUCT AGAIN!
    $actions->perform();

    expect(_(wc()->cart->cart_contents)->mapUsing(product_id: null))->toContain($product->ID);
    expect(wc()->cart->get_total(context: 'edit'))->toBe($expectedCartTotal);
})->with([
    'expected 1 - product exists with the same quantity, nothing gets added' => a(
        existingProductQuantity: 1,
        expectedQuanityToAdd: 1,
        expectedCartTotal: '100.00',
    ),
    'expected 1 - product exists more quantity, nothing gets added' => a(
        existingProductQuantity: 2,
        expectedQuanityToAdd: 1,
        expectedCartTotal: '200.00',
    ),
    'expected 2 - product exists with the less quantity, only 1 gets added' => a(
        existingProductQuantity: 1,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '200.00',
    ),
    'expected 2 - product exists with the same quantity, nothing gets added' => a(
        existingProductQuantity: 2,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '200.00',
    ),
    'expected 2 - product exists with more same quantity, nothing gets added' => a(
        existingProductQuantity: 3,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '300.00',
    ),
]);

//variabke product, other variation with the same prod exists, shall be ignired! 
/** @var UnitTestCase  */
it('applies a variable product, doesn"t exist in the cart', function() {
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
    $productVariation = $this::class::factory()->post->create_and_get(a(
        post_parent: $product->ID,
        post_type: 'product_variation',
        post_title: 'iPod Touch (RED)',
        //meta
        meta_input: a(
            attribute_color: 'red',
            _price: 120,
            _regular_price: 120
        )
    ));

    //load them in the cart
    /*
    wc()->cart->add_to_cart(
        product_id: $product->ID,
        quantity: $existingProductQuantity
    );
     */
    //run the actions
    (object) $cart = new Cart(wc()->cart);

    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddProduct(
                item: new CartItem(
                    product: new Product(wc_get_product($productVariation->ID)),
                    quantity: 1
                ),
                coupon: $coupon,
                cart: $cart
            )
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

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('120.00');

    //LETS RUN THE ACTIONS AGAIN, MAKE SURE WE AIN'T ADDING THE PRODUCT AGAIN!
    $actions->perform();

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('120.00');
});

it('applies a variable product, theres another variation with the same parent but its a different variation so that one should be ignored and therefore we should add this variation', function() {
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
    $productVariation = $this::class::factory()->post->create_and_get(a(
        post_parent: $product->ID,
        post_type: 'product_variation',
        post_title: 'iPod Touch (RED)',
        //meta
        meta_input: a(
            attribute_color: 'red',
            _price: 120,
            _regular_price: 120
        )
    ));
    $productVariation2 = $this::class::factory()->post->create_and_get(a(
        post_parent: $product->ID,
        post_type: 'product_variation',
        post_title: 'iPod Touch (5g)',
        //meta
        meta_input: a(
            attribute_color: 'blue',
            _price: 110,
            _regular_price: 110
        )
    ));

    //load them in the cart
    wc()->cart->add_to_cart(
        product_id: $product->ID,
        quantity: 1,
        variation_id: $productVariation2->ID,
    );
    //run the actions
    (object) $cart = new Cart(wc()->cart);

    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddProduct(
                item: new CartItem(
                    product: new Product(wc_get_product($productVariation->ID)),
                    quantity: 1
                ),
                coupon: $coupon,
                cart: $cart
            )
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

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('230.00');

    //LETS RUN THE ACTIONS AGAIN, MAKE SURE WE AIN'T ADDING THE PRODUCT AGAIN!
    $actions->perform();

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe('230.00');
});

it('applies a VARIABLE product, already exists in the cart (NOT added by us)', function(int $existingProductQuantity, int $expectedQuanityToAdd, string $expectedCartTotal) {
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
    $productVariation = $this::class::factory()->post->create_and_get(a(
        post_parent: $product->ID,
        post_type: 'product_variation',
        post_title: 'iPod Touch (RED)',
        //meta
        meta_input: a(
            attribute_color: 'red',
            _price: 120,
            _regular_price: 120
        )
    ));
    $productVariation2 = $this::class::factory()->post->create_and_get(a(
        post_parent: $product->ID,
        post_type: 'product_variation',
        post_title: 'iPod Touch (5g)',
        //meta
        meta_input: a(
            attribute_color: 'blue',
            _price: 110,
            _regular_price: 110
        )
    ));

    //load them in the cart
    wc()->cart->add_to_cart(
        product_id: $product->ID,
        quantity: 1,
        variation_id: $productVariation2->ID,
    );
    wc()->cart->add_to_cart(
        product_id: $product->ID,
        quantity: $existingProductQuantity,
        variation_id: $productVariation->ID,
    );

    //run the actions
    (object) $cart = new Cart(wc()->cart);

    (object) $coupon = new Coupon(classic: new WC_Coupon('FREE500'), discounts: new WC_Discounts(wc()->cart));

    (object) $actions = new ActionsComposite(
        actions: _(
            new AddProduct(
                item: new CartItem(
                    product: new Product(wc_get_product($productVariation->ID)),
                    quantity: $expectedQuanityToAdd
                ),
                coupon: $coupon,
                cart: $cart
            )
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

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe($expectedCartTotal);

    //LETS RUN THE ACTIONS AGAIN, MAKE SURE WE AIN'T ADDING THE PRODUCT AGAIN!
    $actions->perform();

    expect(_(wc()->cart->cart_contents)->map(fn($item) => [$item['product_id'], $item['variation_id']])->asArray())->toContain([$product->ID, $productVariation->ID]);
    expect(wc()->cart->get_total(context: 'edit'))->toBe($expectedCartTotal);
})->with([
    'expected 1 - product exists with the same quantity, nothing gets added' => a(
        existingProductQuantity: 1,
        expectedQuanityToAdd: 1,
        expectedCartTotal: '230.00',
    ),
    'expected 1 - product exists more quantity, nothing gets added' => a(
        existingProductQuantity: 2,
        expectedQuanityToAdd: 1,
        expectedCartTotal: '350.00',
    ),
    'expected 2 - product exists with the less quantity, only 1 gets added' => a(
        existingProductQuantity: 1,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '350.00',
    ),
    'expected 2 - product exists with the same quantity, nothing gets added' => a(
        existingProductQuantity: 2,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '350.00',
    ),
    'expected 2 - product exists with more same quantity, nothing gets added' => a(
        existingProductQuantity: 3,
        expectedQuanityToAdd: 2,
        expectedCartTotal: '470.00',
    ),
]);

