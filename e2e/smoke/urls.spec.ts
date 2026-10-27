import { expect } from "@wordpress/e2e-test-utils-playwright";
import { test } from "../fixtures/base-utils";
import { randomUUID } from "crypto";

// we don't needa delete the coupons on each run and we can run them in parallel since prods are unique per test and so is the cart sesh.
// like, each coupon url only affects a particular coupon and since we'll have unique coupon and url and prods per test, they will never clash. because on top o' that each test is in a new browser session with a clean cart state. so we're good.

test.describe.configure({ mode: 'parallel' });

// test interactions: the urls button shows up. we can click it and the urls dashboard shows. we can click it to go back to the admin no problem. the admin can be scrolled and the coupon fields can be edited. then we go back to the coupon urls ui.
// test creates a simple one: add the coupon and redirect to cart. repeated for every redirection type.
// test creates a more complex one: add the coupon, a product and redirect to the cart.
// test creates a more complex one: add the coupon, a product, and a success message and redirect to the cart.
// test the default preset.
// test create a url, test that it works and then disable it. the url should not be applied again.
// test nested paths: one/two

test('A simple url from the default template', async ({ product, baseURL, page, wpAdmin, coupon, store }) => {
    const rand = randomUUID().slice(0, 8) // to prevent any chances of parallel clashes.
    const pathToType = `my new url ${rand}`
    const actualPath = `my-new-url-${rand}`
    coupon.code = randomUUID().slice(0, 8)

    const assertUIState = async () => {
        // make sure the path was added correctly
        await expect(pathField()).toHaveValue(actualPath)
        
        // assert visible
        await expect(page.getByRole('heading', { name: 'Add Coupon' })).toBeVisible();
        await expect(page.getByRole('heading', { name: 'Redirect' })).toBeVisible();
    }

    // go to new coupons page
    await wpAdmin.loginAndGoToNewCoupon()

    // add the coupon code
    await page.getByRole('textbox', { name: 'Coupon code' }).fill(coupon.code);
    // click on the urls button
    const state = true
    // click enable
    await page.getByRole('button', { name: 'Configure URL' }).click();
    await expect(page.getByRole('heading', { name: 'URL disabled for this coupon' })).toBeVisible();
    await page.getByRole('button', { name: 'Enable' }).click();

    const pathField = () => page.getByRole('textbox', { name: 'custom/path/' });
    // enter the path
    await pathField().click();
    await pathField().pressSequentially(pathToType) // press it so that we replace spaces with dashes
    // import the preset
    await page.getByRole('button', { name: 'Simple add & redirect' }).click();

    // assert the state:
    await assertUIState()
    // then hit save
    await page.getByRole('button', { name: 'Save' }).click();

    // assert coupon has been saved. 
    await wpAdmin.assertIsClassicView('saved')
    
    // then open the urls screen again
    await page.getByRole('button', { name: 'Configure URL' }).click();
    // make sure out path and actions were saved
    await assertUIState()

    // then go to the store. add the product and then click the coupon url. ensure the coupon has been applied.
    await store.addToCart()
    // finally: let's click the url!
    await page.goto(actualPath)

    // the coupon should of been applied!
    await store.assertCouponIsInAppliedList('cart')

})
