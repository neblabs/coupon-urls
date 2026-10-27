import { expect } from "@wordpress/e2e-test-utils-playwright";
import { test } from "../fixtures/base-utils";

test('Test some small visual elements are rendered correctly.', async ({wpAdmin, page}) => {
    await wpAdmin.loginAndGoToNewCoupon()
    
    await expect(wpAdmin.findMainConfigureOfferButton()).toHaveScreenshot({
        maxDiffPixelRatio: 0.10,
        threshold: 0.25
    })
    
    const state = true
})
