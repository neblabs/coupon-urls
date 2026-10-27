/**
 * External dependencies
 */
import { request, expect } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';
import { deleteAllPostTypeItems, deleteAllOrders, deleteAllCoupons, deleteAllProducts } from './utils';

/**
 *
 * @param {import('@playwright/test').FullConfig} config
 * @returns {Promise<void>}
 */
async function globalSetup(config) {
    const { storageState, baseURL } = config.projects[0].use;
    const storageStatePath =
        typeof storageState === 'string' ? storageState : undefined;

    const requestContext = await request.newContext({
        baseURL,
    });

    const requestUtils = new RequestUtils(requestContext, {
        storageStatePath,
    });

    // Authenticate and save the storageState to disk.
    await requestUtils.setupRest();

    // but first let's check the version installed is the correct
    const plugins = await requestUtils.rest({
        path: '/wp/v2/plugins'
    });
    // probably not wise to hard-code it here...
    const pluginSlug = 'coupon-urls' // dev is coupon-urls and prod is coupon-urls-for-woocommerce

    const {version} = plugins.find(({ plugin }) => plugin.includes(pluginSlug))

    const PLUGIN_VERSION = process.env.PLUGIN_VERSION;
    if (PLUGIN_VERSION) {
        // assert version installed is the same as expected plugin version. Locally the version is dev and non-relevant but this is set on the ci to ensure we test the prod build
        if (PLUGIN_VERSION !== version) {
            // then we have a problem
            throw new Error(`Installed version does not match expected version. Installed: ${version}, Expected: ${PLUGIN_VERSION}`)
        }
    }


    // Reset the test environment before running the tests.
    await Promise.all([
        requestUtils.resetPreferences(),
        deleteAllProducts(requestUtils, expect),
        deleteAllCoupons(requestUtils, expect),
        deleteAllOrders(requestUtils, expect),
    ]);


    await requestUtils.rest({
        path: '/wc/v3/payment_gateways/cod',
        method: 'PUT',
        data: { enabled: true },
    });

    const res = await requestUtils.rest({
        path: '/wc-admin/options',
        method: 'POST',
        data: {
            woocommerce_coming_soon: 'no'
        }
    });

    await requestUtils.rest({
        path: '/wc/v2/settings/general/batch',
        method: 'POST',
        data: {
            update: [
                { id: 'woocommerce_default_country', value: 'US:CA' },
                { id: 'woocommerce_store_city', value: 'Los Angeles' },
                { id: 'woocommerce_store_postcode', value: '90001' },
                { id: 'woocommerce_currency', value: 'USD' },
                { id: 'woocommerce_currency', value: 'USD' },
            ],
        },
    });

    await requestContext.dispose();
}

export default globalSetup;
