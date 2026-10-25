import {mapValues} from 'lodash';

const defaultValuesForType = {
    string: '',
    number: 0,
    float: 0.0,
    collection: [],
}
export const getDefaultOptionsFromOptionsDefinition = (optionsDefinition) => {
    if (Array.isArray(optionsDefinition)) {
        // it has no options!
        return {}
    }

    return mapValues(optionsDefinition, option => {
        if (!option.meta) {
            return getDefaultOptionsFromOptionsDefinition(option)
        }

        const meta = option.meta;

        if (meta._allowed?.length && meta._default === null) {
            return meta._allowed[0]
        }

        return meta._default !== null? meta._default : defaultValuesForType[meta._dataType]
    })
}

export const getOptionComponentsFromOptionDefinition = ({meta}) => {
    if (meta.values) {
        return meta.values
    }

    if (!meta._allowed?.length) {
        return []
    }

    return meta._allowed.map(value => ({value, name: value}))
}

export const pickMetaFromDynamicField = (dynamicFields, options, mainMeta) => {
    const dynamicField = dynamicFields.find(({when: {field, operator, value}}) => {
        const actualValue = options[field]
        const operations = {
            // eslint-disable-next-line eqeqeq
            '==': () => value == actualValue
        }
        return operations[operator]()
    })

    return dynamicField && {
        ...mainMeta,
        ...dynamicField
    }
}