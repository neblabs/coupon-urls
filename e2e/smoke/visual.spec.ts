import { expect } from "@wordpress/e2e-test-utils-playwright";
import { test } from "../fixtures/base-utils";

test('Test some small visual elements are rendered correctly.', async ({wpAdmin, page}) => {
    await wpAdmin.loginAndGoToNewCoupon()
    
    // open the urls ui
    await page.getByRole('button', { name: 'Configure URL' }).click()

    await expect(await page.getByRole('button', { name: 'Enable' })).toHaveScreenshot({
        maxDiffPixelRatio: 0.10,
        threshold: 0.25
    })
})
