import { createDefaultAction, getActionForType } from "../utils/actionHelpers";
import { copyOfTheState } from "../utils/state";

export default class Reducers
{
    static getReducers()
    {
        return {
            'queryParameters/add': Reducers.onNewQueryParameter,
            'queryParameters/update': Reducers.onQueryParameterUpdate,
            'queryParameters/delete': Reducers.onQueryParameterDelete,
            'URI/value/update': Reducers.onURIValueUpdate,
            'URI/type/update': Reducers.onURITypeUpdate,
            'actions/add': Reducers.onNewAction,
            'actions/set': Reducers.onActionsSet,
            'actions/update': Reducers.onActionUpdate,
            'actions/delete': Reducers.onActionDelete,
            'options/isEnabled/update': Reducers.onIsEnabledOptionUpdate
        }
    }

    static onNewQueryParameter(state, payload) 
    {
        return copyOfTheState(state, newState => {
            newState.queryParameters.push({key: '', value: ''}) 
        })
    }

    static onQueryParameterUpdate(state, {payload: {keyOrValueType, value, index}}) 
    {
        return copyOfTheState(state, newState => {
            newState.queryParameters[index][keyOrValueType] = value
        })
    }

    static onQueryParameterDelete(state, {payload: {index}}) 
    {
        return copyOfTheState(state, newState => {
            newState.queryParameters = newState.queryParameters.filter(
                (value, queryParameterIndex) => queryParameterIndex !== index
            )
        })
    }

    static onURIValueUpdate(state, {payload: {value}}) 
    {
        return copyOfTheState(state, newState => {
            newState.uri.value = value
        })
    }

    static onURITypeUpdate(state, {payload: {type}}) 
    {
        return copyOfTheState(state, newState => {
            newState.uri.type = type
        })
    }

    static onNewAction(state, {payload: {type}}) 
    {
        return copyOfTheState(state, ({actions}) => {
            actions.push(createDefaultAction(type))
        })
    }

    static onActionsSet(state, {payload: {actions: newActions}}) 
    {
        return copyOfTheState(state, (copyOfTheState) => {
            copyOfTheState.actions = newActions
        })
    }

    static onActionUpdate(state, {payload: {type, optionType, value}}) 
    {
        return copyOfTheState(state, ({actions}) => {
           getActionForType(actions, type).options[optionType] = value
        })
    }

    static onActionDelete(state, {payload: {type}}) 
    {
        return copyOfTheState(state, (state) => {
            state.actions = state.actions.filter(action => {
                return action.type !== type
            })
        })
    }

    static onIsEnabledOptionUpdate(state, {payload: {isEnabled}}) 
    {
        return copyOfTheState(state, ({options}) => {
            options.isEnabled = isEnabled
        })
    }
}