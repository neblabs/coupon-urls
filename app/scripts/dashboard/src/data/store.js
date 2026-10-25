import { createStore } from 'redux';
import _ from 'lodash';
import { state } from '../CouponURLs';

//
// a default uri is set below if the state didnt have one (for new coupons!)
const defaultState = state || {} || {
    queryParameters: [
    ],
    uri: {
        type: 'path',
        value: 'offers/FREE500'
    },
    actions: [
       {
      type: 'AddCoupon',
      options: {}
    },
    {
      type: 'AddProduct',
      options: {
          id: 0,
          quantity: 1
      }
    },
    {
      type: 'CouponToBeAddedNotificationMessage',
      options: {
        message: 'You have a special offer available! It will be applied when your cart meets the requirements.'
      }
    },
    {
      type: 'CouponAddedToCartExtraNotificationMessage',
      options: {
        message: 'Congratulations! Your special offer has been applied!'
      }
    },
    {
      type: 'Redirection',
      options: {
        type: 'postType',
        value: ''
      }
    }
    ],
    options: {
        isEnabled: true
    }
};

if (!defaultState.uri.type) {
    defaultState.uri = {
        type: 'path',
        value: ''
    }
}

const reducerFiles = require.context('../reducers', true, /\.js$/);

let keys = reducerFiles.keys();

const Reducers = keys.map(reducerFiles).map(module => module.default);

const reducer = (state = defaultState, action) => {
    const registeredReducers = Reducers.map(
        Reducer => Reducer.getReducers()
    );
    const actionTypeReducersMap = {};

    for (let reducerGroup of registeredReducers) {
        for (let actionType in reducerGroup) {
            if (typeof actionTypeReducersMap[actionType] === 'undefined') {
                actionTypeReducersMap[actionType] = [];
            }
            
            actionTypeReducersMap[actionType].push(reducerGroup[actionType]);            
        }
    }

    let newState = _.cloneDeep(state);

    const executeReducers = (reducers, newState, action) => {
        for (let reducer of reducers) {
            newState = reducer(newState, action);
        }

        return newState;
    };

    if (!action.type.startsWith('@@')) {
        newState = executeReducers(actionTypeReducersMap[action.type], newState, action)

        const afterActionReducers = actionTypeReducersMap[`after:${action.type}`];

        if (afterActionReducers?.length) {
            newState = executeReducers(afterActionReducers, newState, action);
        }

        const afterStateChange = actionTypeReducersMap['__AFTER_STATE_CHANGE__'];

        if (afterStateChange?.length) {
            newState = executeReducers(afterStateChange, newState, action);
        }
        /*
        for (let reducer of actionTypeReducersMap[action.type]) {
            newState = reducer(newState, action);

            if (actionTypeReducersMap[action.type].length) {

            }
        }*/
    }
    return newState;
}

export default createStore(reducer, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__({trace:true}));

//this one we want to keep it separate from the main store since unlinkedState.body may be HUGE
//we don't want to be passing it around for performance reasons
export const unlinkedState = {subject: '', body: ''}