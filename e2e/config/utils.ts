import { type RequestUtils } from '@wordpress/e2e-test-utils-playwright';
import { Expect } from 'playwright/test';

/**
 * Deletes all items of a given REST route and cleans up their postmeta.
 * @param requestUtils Playwright requestUtils fixture
 * @param restRoute e.g., '/wp/v2/posts', '/wc/v3/products', or custom CPT endpoints
 */
export async function deleteAllPostTypeItems(
    requestUtils: RequestUtils,
    restRoute: string, 
    expect?: Expect
) {
    const items = await requestUtils.rest( { path: `${ restRoute }?per_page=100` } );
    if ( ! Array.isArray( items ) || items.length === 0 ) return;

    for ( const item of items ) {
        const response = await requestUtils.rest( {
            path: `${ restRoute }/${ item.id }?force=true`,
            method: 'DELETE',
        } );

        //expect?.( response ).toHaveProperty( 'deleted', true );
    }
}

export async function deleteAllProducts(requestUtils: RequestUtils, expect: Expect) {
    await deleteAllPostTypeItems( requestUtils, '/wc/v3/products', expect );        
}

export async function deleteAllCoupons(requestUtils: RequestUtils, expect: Expect) {
    await deleteAllPostTypeItems( requestUtils, '/wc/v3/coupons', expect );        
}

/**
 * Deletes all WooCommerce orders (works for HPOS and post-type orders).
 */
export async function deleteAllOrders( requestUtils: RequestUtils ) {
    const orders = await requestUtils.rest( { path: '/wc/v3/orders?per_page=100' } );
    if ( ! Array.isArray( orders ) || orders.length === 0 ) return;

    for ( const order of orders ) {
        await requestUtils.rest( {
            path: `/wc/v3/orders/${ order.id }?force=true`,
            method: 'DELETE',
        } );
    }
}

interface ProductData {
    name: string;
    type?: 'simple' | 'grouped' | 'external' | 'variable';
    regular_price: string;
    description?: string;
    status?: 'publish' | 'draft' | 'pending' | 'private';
    [key: string]: unknown;
}

export interface CreateProductPayload {
    name: string;
    type?: 'simple' | 'grouped' | 'external' | 'variable';
    status?: 'draft' | 'pending' | 'private' | 'publish';
    regular_price?: string;
    sale_price?: string;
    description?: string;
    short_description?: string;
    sku?: string;
    manage_stock?: boolean;
    stock_quantity?: number;
    [key: string]: unknown;
}

export interface WooCommerceProduct extends CreateProductPayload {
    id: number;
    slug: string;
    permalink: string;
    price: string;
    on_sale: boolean;
    purchasable: boolean;
    date_created: string;
}

export async function createProduct(
    requestUtils: RequestUtils,
    data: CreateProductPayload
): Promise<WooCommerceProduct> {
    const defaultData: Partial<CreateProductPayload> = {
        type: 'simple',
        status: 'publish',
    };

    return await requestUtils.rest<WooCommerceProduct>({
        path: '/wc/v3/products',
        method: 'POST',
        data: { ...defaultData, ...data },
    });
}
