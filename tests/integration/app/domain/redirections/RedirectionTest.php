<?php declare(strict_types=1);

use CouponURLs\App\Creation\Coupons\CouponFactory;
use CouponURLs\App\Creation\Coupons\CouponsFactory;
use CouponURLs\App\Domain\Actions\ActionsComposite;
use CouponURLs\App\Domain\Actions\ActionsRunnerComposite;
use CouponURLs\App\Domain\Actions\CouponAdders\AddCoupon;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponCartAdder;
use CouponURLs\App\Domain\Actions\CouponAdders\CouponToBeAddedCartAdder;
use CouponURLs\App\Domain\Actions\NonRestrictableActionRunner;
use CouponURLs\App\Domain\Actions\Redirections\Redirection;
use CouponURLs\App\Domain\Actions\RestrictableActionRunner;
use CouponURLs\App\Domain\Carts\Cart;
use CouponURLs\App\Domain\Coupons\Coupon;
use CouponURLs\App\Domain\Coupons\CouponsToBeAdded;
use CouponURLs\App\Domain\Redirections\HomePageURL;
use CouponURLs\App\Domain\Redirections\PlainURL;
use CouponURLs\App\Domain\Redirections\PostTypeRedirectionValidator;
use CouponURLs\App\Domain\Redirections\PostTypeURL;
use CouponURLs\App\Domain\Redirections\WordPressURLRedirector;
use CouponURLs\Original\System\Abilities\Exitable;
use CouponURLs\Original\System\Functions\GlobalFunctionWrapper;
use CouponURLs\Original\Validation\Validators\PassingValidator;
use PHPUnit\Framework\MockObject\MockObject;
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

// redirection to homepage

/** @var UnitTestCase  */
it('redirection to post type ( = page)', function() {
    /** @var \Mockery\MockInterface&Exitable */
    (object) $exiter = Mockery::mock(Exitable::class);
    /** @var MockObject&GlobalFunctionWrapper */
    (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
    //save the products in them database
    $page = $this::class::factory()->post->create_and_get(a(
        post_type: 'page',
        post_title: 'your offer is ready!',
    ));
    //load them in the cart
    //run the actions

    (object) $actions = new ActionsComposite(
        actions: _(
            new Redirection(
                new WordPressURLRedirector(
                    url: new PostTypeURL($page->ID),
                    redirectionValidator: new PostTypeRedirectionValidator(
                        $page->ID
                    ),
                    exiter: $exiter,
                    _: $globalFunctionWraper
                )
            )
        ),
        actionsRunner: new ActionsRunnerComposite(
            actionRunners: _(
                new RestrictableActionRunner,
                new NonRestrictableActionRunner
            )
        )
    );

    //check that the redirection took place!
    $exiter->shouldReceive('exit')->once();

    $globalFunctionWraper->expects($this->exactly(1))
                         ->method('__call')
                         ->with(
                                'wp_redirect', 
                                a(
                                    location: "http://example.org/?page_id={$page->ID}"
                                )
                            )->willReturn(true);

    $actions->perform();
});

/** @var UnitTestCase  */
it('redirection to a random url', function() {
    /** @var \Mockery\MockInterface&Exitable */
    (object) $exiter = Mockery::mock(Exitable::class);
    /** @var MockObject&GlobalFunctionWrapper */
    (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
    //save the products in them database
    $page = $this::class::factory()->post->create_and_get(a(
        post_type: 'page',
        post_title: 'your offer is ready!',
    ));
    //load them in the cart
    //run the actions

    (object) $actions = new ActionsComposite(
        actions: _(
            new Redirection(
                new WordPressURLRedirector(
                    url: new PlainURL('https://google.com/redirected!'),
                    redirectionValidator: new PassingValidator,
                    exiter: $exiter,
                    _: $globalFunctionWraper
                )
            )
        ),
        actionsRunner: new ActionsRunnerComposite(
            actionRunners: _(
                new RestrictableActionRunner,
                new NonRestrictableActionRunner
            )
        )
    );

    //check that the redirection took place!
    $exiter->shouldReceive('exit')->once();

    $globalFunctionWraper->expects($this->exactly(1))
                         ->method('__call')
                         ->with(
                                'wp_redirect', 
                                a(
                                    location: "https://google.com/redirected!"
                                )
                            )->willReturn(true);

    $actions->perform();
});

it('redirection to home page', function() {
    /** @var \Mockery\MockInterface&Exitable */
    (object) $exiter = Mockery::mock(Exitable::class);
    /** @var MockObject&GlobalFunctionWrapper */
    (object) $globalFunctionWraper = $this->createMock(GlobalFunctionWrapper::class);
    //save the products in them database
    $page = $this::class::factory()->post->create_and_get(a(
        post_type: 'page',
        post_title: 'your offer is ready!',
    ));
    //load them in the cart
    //run the actions

    (object) $actions = new ActionsComposite(
        actions: _(
            new Redirection(
                new WordPressURLRedirector(
                    url: new HomePageURL,
                    redirectionValidator: new PostTypeRedirectionValidator(
                        $page->ID
                    ),
                    exiter: $exiter,
                    _: $globalFunctionWraper
                )
            )
        ),
        actionsRunner: new ActionsRunnerComposite(
            actionRunners: _(
                new RestrictableActionRunner,
                new NonRestrictableActionRunner
            )
        )
    );

    //check that the redirection took place!
    $exiter->shouldReceive('exit')->once();

    $globalFunctionWraper->expects($this->exactly(1))
                         ->method('__call')
                         ->with(
                                'wp_redirect', 
                                a(
                                    location: "http://example.org"
                                )
                            )->willReturn(true);

    $actions->perform();
});