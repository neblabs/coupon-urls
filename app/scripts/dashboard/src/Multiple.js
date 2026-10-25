import React, {Component} from 'react';
import { connect } from "react-redux";
import classNames from 'classnames';
import Select from 'react-select';
import AsyncSelect from 'react-select/async';
import { __ } from './globals';

class Multiple extends Component
{
    state = {
        focused: false
    };

    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        const SelectComponent = this.props.isAsync? AsyncSelect : Select;
        const isMultiple = typeof this.props.isMultiple === 'boolean'? this.props.isMultiple : true;
        const beforeSendingNewValue = this.props.beforeSendingNewValue || (value => value);
        const widthClasses = classNames({
            'w-full': !this.props.width,
            [this.props.width || '']: this.props.width
        });
        const placeholder = this.props?.placeholder || __('Type to search...')
        return (
            <div className={`flex flex-col space-y-1 ${widthClasses || ''}`}>
                <SelectComponent 
                    {...this.props}
                    value={this.props.getValue().label? this.props.getValue() : null}
                    filterOption={null}
                    className={widthClasses}
                    classNamePrefix="cp-select"
                    placeholder={placeholder}
                    noOptionsMessage={({inputValue}) => !inputValue ? __('Type to search...') : __('Nothing found')}
                    isMulti={isMultiple}
                    isClearable={this.props.isMultiple? false : false}
                    isSearchable={typeof this.props.isSearchable === 'boolean'? this.props.isSearchable : true}
                    loadOptions={this.props.isAsync? this.props.options : null}
                    options={!this.props.isAsync? this.props.options : null
                        /** struct:
                            [
                              { value: 'chocolate', label: 'Chocolate' },
                              { value: 'strawberry', label: 'Strawberry' },
                            ]
                         */
                    } 
                    onChange={(valueOrValues) => {
                        let newValueOrValues = valueOrValues;

                        if (Array.isArray(valueOrValues)) {
                            newValueOrValues = valueOrValues.map(({value}) => value)
                        } else {
                            newValueOrValues = valueOrValues.value;
                        }

                        return this.props.newSelectValue(
                           beforeSendingNewValue(newValueOrValues), 
                       )
                    }}
                />
                {this.props?.labels?.bottom? (
                    <div className={widthClasses}>{this.props.labels.bottom}</div>
                ) : ''}
            </div>
        );
    }
}

export default connect(Multiple.mapStateToProps, {})(Multiple);