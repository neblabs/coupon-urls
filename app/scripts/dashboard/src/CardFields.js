import {mapValues} from 'lodash';
import Input from './Input';
import Multiple from './Multiple';
import { $ } from './globals';
import { addPreloadedItems, getPreloadedItemForValue } from './utils/state';
import { useDispatch } from 'react-redux';
import actions from './actions/Actions';
import { getActionComponentForType } from './utils/actionHelpers';
import Select from './Select';
import { getOptionComponentsFromOptionDefinition, pickMetaFromDynamicField } from './utils/optionsHelper';
const CardFields = ({component: {type, options}, data}) => {
    const dispatch = useDispatch()

    return <div className="flex flex-row space-x-2 items-center px-3 pb-2">
                {Object.values(mapValues(options, ({meta}, optionId) => {
                    const value = data[optionId]
                    const updateAction = value => dispatch(actions.updateAction(type, optionId, value))

                    if(meta?.dynamicFields?.length) {
                        meta = pickMetaFromDynamicField(meta.dynamicFields, data, meta)

                        if (!meta) {
                            //no match so dont show
                            return ''
                        }
                    }

                    if (meta?.field?.searchable) {
                        return <Multiple 
                            isAsync={true} 
                            isMultiple={false}
                            placeholder={meta?.field?.placeholder}
                            getValue={() => {
                                return {
                                    value, 
                                    label: getPreloadedItemForValue(type, value)?.label
                                }
                            }}
                            isSearchable={true}
                            options={value => new Promise((resolve, reject) => {
                                $.ajax({
                                    method:meta.field.searchable?.method || 'GET',
                                    url: meta.field.searchable.url,
                                    data: mapValues(meta.field.searchable.data, dataValue => typeof dataValue === 'string'? dataValue.replace('((value))', value) : dataValue),
                                    dataType: 'json',
                                    success: (response) => {
                                        if (typeof response === 'object') {
                                            //this accepts two formats:
                                            // A) [{ID: 10, title: 'The title!'}]
                                            // B) {10: 'The title!'}
                                            let responseItems;

                                            if (Array.isArray(response)) {
                                                responseItems = response.map(({ID, title}) => ({
                                                    value: ID,
                                                    label: title
                                                }))
                                            } else {
                                                responseItems = Object.keys(response).map(id => ({
                                                    value: id, 
                                                    label: response[id],//getProductNameById(id)
                                                }));
                                            }

                                            addPreloadedItems(type, responseItems)
                                            resolve(responseItems)
                                        }
                                    },
                                })
                            })}
                            newSelectValue={updateAction}
                        />
                    }

                    if (meta._dataType !== 'collection' && !meta._allowed?.length) {
                        return <Input 
                            getValue={() => value}
                            getPlaceholder={() => meta?.field?.placeholder || ''}
                            newSelectValue={updateAction}
                            labels={meta?.field?.labels || {}}
                            width={meta?.field?.width}
                            subtype={meta._dataType}
                        />
                    }

                    return <Select getValue={() => value} 
                            labels={getActionComponentForType(type).options[optionId]?.meta?.field?.labels}
                            options={getOptionComponentsFromOptionDefinition(getActionComponentForType(type).options[optionId]
                            )} 
                            onChange={updateAction}
                            />
                }))}
            </div>;
}

export default CardFields;