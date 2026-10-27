import { test as base, expect, RequestUtils } from "@wordpress/e2e-test-utils-playwright";
import { createProduct, type WooCommerceProduct } from "../config/utils";
import { Locator } from "@playwright/test";
import { randomUUID } from "crypto";

type WPAdminUtils = {
    saveCoupon: () => Promise<any>;
    assertIsClassicView: (newOrSaved: 'new' | 'saved') => Promise<any>
    login: () => Promise<any>,
    loginAndGoToNewCoupon: () => Promise<any>,
    logout: () => Promise<void>,
    goToNewCoupon: () => void,
}

type StoreUtils = {
    addToCart: (product?: WooCommerceProduct) => Promise<void>,
    assertCouponIsInAppliedList: (where?: 'cart' | 'checkout') => Promise<any>,
}

type NavUtils = {
    ensurePage: (path: string) => Promise<void>
}

type BaseFixture = {
    nav: NavUtils,
    product: WooCommerceProduct,
    createProduct: (opts?: { name?: string, price?: string }) => Promise<WooCommerceProduct>,
    wpAdmin: WPAdminUtils,
    store: StoreUtils,
    coupon: { code: string }
}

type Coupon = {
    code: string;
};

export const test = base.extend<BaseFixture>({
    /* 
        This provides a base product useful for most tests 
    */
    createProduct: async ({ requestUtils }, use) => {
        await use(async ({ name, price } = {}) => {
            const basename = name || 'Red Shirt'

            return await createProduct(requestUtils, {
                name: `${basename} ${randomUUID().slice(0, 8)}`,
                regular_price: price || '19.99'
            })
        })
    },
    product: async ({ requestUtils, createProduct }, use) => {
        await use(await createProduct())
    },
    coupon: ({ }, use) => {
        // for mutating in the test
        const coupon: Coupon = { code: '' }

        use(coupon)
    },
    nav: async ({ page }, use) => {
        use({
            ensurePage: async (path) => {
                const formatPath = (_path: string) => {
                    return `/${_path}`.toLowerCase().replaceAll(/\/+/g, '/').replace(/\/$/, '')
                }

                if (formatPath(new URL(page.url()).pathname) !== formatPath(path)) {
                    await page.goto(path)
                }
            }
        })
    },
    wpAdmin: async ({ page, coupon, requestUtils, product,  }, use) => {
        const login = async () => {
            await page.goto(`/wp-login.php`)

            await page.getByRole('textbox', { name: 'Username or Email Address' }).click();
            await page.getByRole('textbox', { name: 'Username or Email Address' }).fill('admin');
            await page.getByRole('textbox', { name: 'Username or Email Address' }).press('Tab');
            await page.getByRole('textbox', { name: 'Password' }).fill('password');
            await page.getByRole('button', { name: 'Log In' }).click();
        }

        const newCouponUrlPath = `/wp-admin/post-new.php?post_type=shop_coupon`;
        const goToNewCoupon = async () => await page.goto(newCouponUrlPath)

        const cursorOnCenter = async (height = 600) => {
            // height: no less than 550-ish so that the classic elements we are using to check if we are in classic mode are visible without scrolling
            await page.mouse.move(640, height / 2);
        }
        const loginAndGoToNewCoupon = async () => {
            if (page.url().includes(newCouponUrlPath)) {
                // we're already logged in and loaded.
                return
            }
            await login()
            await goToNewCoupon()
        }

        const findMainConfigureOfferButton = () => page.getByRole('button', { name: 'Configure Offer' })

        use({
            login,
            goToNewCoupon,
            loginAndGoToNewCoupon,
            logout: async () => {
                // this is only only line and potentially shallow
                // but it is in its own method in case we need to refactor the implementation it in the future
                await page.context().clearCookies();
            },
            saveCoupon: async () => {
                await page.getByRole('button', { name: 'Save', exact: true }).click();

                // make sure we are in the saved page and not in the base new coupon page in case the save
                // button didn't trigger the save action
                await expect(page).toHaveURL(/post\.php\?post=\d+&action=edit/);
                await expect(page.getByText('saved', { exact: false })).toBeVisible()
            },
            assertIsClassicView: async (newOrSaved) => {
                await expect(page.getByRole('heading', { name: newOrSaved === 'new' ? 'Add new coupon' : 'Edit coupon' })).toBeVisible();
                await expect(page.getByRole('heading', { name: 'Publish' })).toBeVisible();
                await expect(page.getByText(newOrSaved === 'new' ? 'Status: Draft' : 'Status: Published')).toBeVisible();
            },
        } as WPAdminUtils)
    },
    store: async ({ page, coupon, product, nav }, use) => {
        const findProductCartLineItem = (_product = product) => {
            return page
                .locator('.wc-block-cart-items__row')
                .filter({ has: page.locator('.wc-block-components-product-name', { hasText: _product.name }) });
        }

        const ensureItsIn = async (where?: 'cart' | 'checkout') => {
            if (where === 'cart') {
                await nav.ensurePage('/cart')
            } else if (where === 'checkout') {
                await nav.ensurePage('/checkout')
            }
        }
        const assertCouponIsInAppliedList = async (where?: 'cart' | 'checkout') => {
            /*
                works for both cart and checkout pages!
             */
            // now assert that the coupon has been added
            // so using classes is an anti pattern in playwright how-ever this is code that we do not control
            // and so we have two options: use the label: "remove coupon: code" or use the css classes
            // we have to decide which is more reliable in the sense that which is less likely to change
            // i figure css classes are more stable because they're part of the contract/interface
            // and a text label isn't. so for reliability's sake we'll use the one less likely to change.
            where && (await ensureItsIn(where))
            const discountSection = page.locator('.wc-block-components-totals-discount__coupon-list-item');
            await expect(discountSection.getByText(new RegExp(`^${coupon.code}$`, 'i'), { exact: true })).toBeVisible();
        }
        use({
            assertCouponIsInAppliedList,
            addToCart: async (productToAdd = product) => {
                await nav.ensurePage('/shop')
                await page.getByRole('link', { name: productToAdd.name }).click();
                // we are now in the single prod page, the following button needs to be the very first one when there are several products
                await page.getByRole('button', { name: 'Add to cart' }).first().click();
            }
        })
    },
})
