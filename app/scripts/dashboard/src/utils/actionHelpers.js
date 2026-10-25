import { components } from "../CouponURLs"
import { getDefaultOptionsFromOptionsDefinition } from "./optionsHelper"

export const createDefaultAction = (actionType) => {
    return {
        type: actionType,
        options: getDefaultOptionsFromOptionsDefinition(
            components.actions.find(({type}) => type === actionType).options
        )
    }
}

export const getActionComponentForType = actionType => getActionForType(components.actions, actionType)
export const getActionForType = (actions, actionType) => actions.find(({type}) => type === actionType)
