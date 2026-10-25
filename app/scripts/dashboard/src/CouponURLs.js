import {cloneDeep} from  'lodash'

export const state = CouponURLs.state
export const textDomain = CouponURLs.textDomain;
export const urls = CouponURLs.urls;
export const components = CouponURLs.components;
export const uris = CouponURLs.components.uris;
export const preloadedItems = CouponURLs.preloadedItems;
export const security = CouponURLs.security;
export const features = CouponURLs.features;
export const __ = (window.wp.i18n && window.wp.i18n.__) || ((text) => text);
export const initialState = cloneDeep(state)