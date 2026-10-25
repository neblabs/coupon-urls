import {cloneDeep} from 'lodash';
import { preloadedItems } from '../CouponURLs';

export const copyOfTheState = (state, updater) => {
    // we're cloning the main state, VERY important so that 
    // changes are made inmutably (I assume for the view to update correctly)
    const copyOfTheSate = cloneDeep(state);

    updater(copyOfTheSate);

    return copyOfTheSate;
}

export const addPreloadedItems = (componentType, items) => {
    const preloadedItems = getPreloadedItems(componentType)

    for (let item of items) {
        // eslint-disable-next-line eqeqeq
        if (!preloadedItems.find(({value}) => item.value == value)) {
            preloadedItems.push(item)
        }
    }
}

export const getPreloadedItems = (componentType) => {
    if (!preloadedItems[componentType]) {
        preloadedItems[componentType] = [];
    }

    return preloadedItems[componentType]
}

// eslint-disable-next-line eqeqeq
export const getPreloadedItemForValue = (componentType, itemValue) => getPreloadedItems(componentType).find(({value}) => value == itemValue)