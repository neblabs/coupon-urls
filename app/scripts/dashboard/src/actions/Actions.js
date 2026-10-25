const actions = {
    newQueryParameter() 
    {
        return {
            type: 'queryParameters/add',
            payload: {
            },
        };
    },

    updateQueryParameter(keyOrValueType, value, index) 
    {
        return {
            type: 'queryParameters/update',
            payload: {
                keyOrValueType,
                value,
                index
            },
        };
    },

    deleteQueryParameter(index) 
    {
        return {
            type: 'queryParameters/delete',
            payload: {
                index
            },
        };
    },

    updateURIValue(value) 
    {
        return {
            type: 'URI/value/update',
            payload: {
                value
            },
        };
    },

    updateURIType(type) 
    {
        return {
            type: 'URI/type/update',
            payload: {
                type
            },
        };
    },

    addAction(type) 
    {
        return {
            type: 'actions/add',
            payload: {
                type
            },
        };
    },

    setActions(actions) 
    {
        return {
            type: 'actions/set',
            payload: {
                actions
            },
        };
    },

    updateAction(type, optionType, value) 
    {
        return {
            type: 'actions/update',
            payload: {
                type,
                optionType,
                value
            },
        };
    },

    removeAction(type) 
    {
        return {
            type: 'actions/delete',
            payload: {
                type
            },
        };
    },

    enable() 
    {
        return {
            type: 'options/isEnabled/update',
            payload: {
                isEnabled: true
            },
        };
    },

    disable() 
    {
        return {
            type: 'options/isEnabled/update',
            payload: {
                isEnabled: false
            },
        };
    },
};

export default actions;