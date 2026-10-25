import React, {Component, Fragment} from 'react';
import { connect } from "react-redux";
import classNames from 'classnames';
//import DatePicker from "react-datepicker";

class Input extends Component
{
    state = {
        focused: false
    };

    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <>
            <div className={classNames("flex flex-col space-y-1", {
                'min-w-22': !this.props.width && !this.props.customWidth,
                'w-half': this.props.width === 'half',
                'w-full': this.props.width === 'full',
                [this.props.customWidth]: this.props.customWidth,
                [this.props.className]: this.props.className
            })}>
                {this.props.labels?.top? (
                        <span className="px-2">{this.props.labels?.top}</span>
                    ) : ''}
                <div className="flex flex-row items-center w-full">
                    {this.props.labels?.left? (
                        <span className="mr-1">{this.props.labels?.left}</span>
                    ) : ''}
                    <div className={classNames('flex flex-row h-9 items-center rounded-4 border-px  overflow-hidden  px-2 text-left rounded-4 border-px text-base bg-white space-x-1', {
                        'border-gray-200': !this.state.focused,
                        'border-blue-normal': this.state.focused,
                        'w-full': !this.props.inputWidth,
                        [this.props.inputWidth]: this.props.inputWidth
                    })}>
                        <span className="text-4x text-gray-500 font-light antialiased">{this.props.labels?.inside?.left || ''}</span>
                        <input type={this.props.subtype || 'text'} 
                               className="text-gray-700 border-none w-full bg-transparent p-0 hover:border-none hover:border-transparent focus:border-transparent focus:outline-none focus:shadow-none"
                               value={this.props.getValue() === 0? '' : this.props.getValue()} 
                               placeholder={this.props.getValue() === 0? 0 : (this.props.getPlaceholder?.() || '')}
                               onFocus={() => this.setState({focused: true})}
                               onBlur={() => this.setState({focused: false})}
                               onChange={({target}) => this.props.newSelectValue(
                                   target.value, this.props.propertyName
                               )}
                        />
                        <span className={classNames({
                            'text-4x text-gray-500 font-light antialiased whitespace-nowrap': !this.props.labels?.inside?.rightClasses,
                            [this.props.labels?.inside?.rightClasses || '']: this.props.labels?.inside?.rightClasses

                        })}>{this.props.labels?.inside?.right || ''}</span>
                    </div>
                    {this.props.labels?.right? (
                        <span className="ml-2">{this.props.labels?.right}</span>
                    ) : ''}
                </div>
            </div>
            </>
        );
    }
}

export default connect(Input.mapStateToProps, {})(Input);