import React, {useState} from 'react';
import Tippy from '@tippyjs/react/headless'; 
import classNames from 'classnames';
import { __ } from './CouponURLs';
import { CouponURLs } from './globals';

export const TipMenu = ({button, components, selectedIds = [], onSelection, idKey = 'id'}) => {
    const canBeUsed = id => !selectedIds.includes(id)
    const [isOpen, setisOpen] = useState(false);
    return <Tippy
        allowHTML={true}
        followCursor={true}
        placement="auto"
        interactive={true}
        visible={isOpen}
        onClickOutside={setisOpen.bind(this, false)}
        render={attrs => (
        <div className={classNames(
            'z-1000 px-4 sm:px-0',
            {
                'w-[640px]': true
            }
        )} {...attrs}>
          <div className="overflow-hidden rounded-2 shadow-lg bg-gray-150">
                <div className="flex flex-row space-x-1">
                    <div className="flex flex-col w-full">
                        <h1 className="text-2x text-gray-500 px-4 py-3">{__('Actions', CouponURLs.textDomain)}</h1>
                        <div className={classNames(
                            'relative w-full h-full grid gap-4 bg-white p-4',
                            {
                                'grid-cols-2': true
                            }
                        )}>
                            {(components.map((component) => {
                                const {name, description = ''} = component
                                const id = component[idKey]
                                return (
                                    <button
                                    key={id}
                                    disabled={canBeUsed(id)? false : true}
                                    onClick={() => {
                                        setisOpen(false)
                                        onSelection(id)
                                    }}
                                    className={classNames(
                                        "flex items-center p-2 -m-3 transition duration-150 ease-in-out rounded-lg hover:bg-gray-50 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50",
                                        {
                                            'opacity-30': !canBeUsed(id)
                                        }
                                    )}
                                  >
                                    <div className="flex items-center justify-center flex-shrink-0 rounded-3 w-10 h-10 bg-gray-300 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" />
                                        </svg>
                                    </div>
                                    <div className="flex flex-col items-start justify-start ml-4">
                                      <p className="text-sm font-medium text-gray-900 text-left">
                                        {name}
                                      </p>
                                      <p className="text-sm text-gray-500 text-left">
                                        {description}
                                      </p>
                                    </div>
                                  </button>
                                )
                            }))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        )}
      >
        <div onClick={setisOpen}>
            {button}
        </div>
      </Tippy>
}