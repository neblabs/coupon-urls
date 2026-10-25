import React, {Component, Fragment} from 'react';
import { connect } from "react-redux";
import { Listbox, Transition } from '@headlessui/react'

//const {newSelectValue} = FieldsActions

class Select extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className="flex flex-col justify-center">
                {this.props.labels?.top? <div className="px-3 smaller-1 capitalize text-gray-300 mb-1">{this.props.labels?.top}</div> : ''}
                <div className="relative text-gray-650 flex flex-row space-x-1 items-center">
                    <Listbox value={this.props.getValue()} onChange={this.props.onChange}>
                        {this.props.button? this.props.button(Listbox.Button) : (
                            <Listbox.Button className={`relative ${this.props.width === 'auto'? 'w-auto' : 'w-full'} space-x-1 flex flex-row justify-between items-center px-3 h-9 text-left rounded-4 border-px border-gray-200 focus:border-blue-normal focus:outline-none shadow-input cursor-default hover:cursor-pointer text-base select-none whitespace-nowrap`}>
                        <span>{this.props.options.find((option) => this.props.getValue() === (typeof option.id !== 'undefined'? option.id : option.value)).name}</span>{this.getButtonIcon()}
                        </Listbox.Button>)}
                      <Transition appear as={Fragment}>
                          <Listbox.Options className="absolute top-0 py-0 overflow-auto text-base bg-white border border-gray-100 rounded-3 py-1 shadow-lg max-h-120 max-w-100 focus:outline-none z-50">
                              {this.props.options.map(({id = null, value = null, name, description = ''}) => (
                              <Listbox.Option
                                key={id ?? value}
                                value={value === null? id: value}
                                className="mb-0 min-h-9 whitespace-nowrap flex flex-col mx-1 px-2 rounded-4 py-1 hover:cursor-pointer hover:bg-gray-150 select-none"
                              >
                                <p className="text-gray-550 text-base">{name}</p>
                                {description? <p className='w-full text-gray-400 text-smaller-1'>{description}</p>: ''}
                              </Listbox.Option>
                              ))}
                          </Listbox.Options>
                      </Transition>
                    </Listbox>
                    {this.props.labels?.right? <div className="smaller-1 text-gray-400">{this.props.labels?.right}</div> : ''}
                </div>
                {this.props.labels?.bottom? <div className="px-3 smaller-1 text-gray-300 mb-1 mt-1">{this.props.labels?.bottom}</div> : ''}
            </div>
        );
    }
    getButtonIcon() 
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
            </svg>
        )
    }
}

export default connect(Select.mapStateToProps, {})(Select);