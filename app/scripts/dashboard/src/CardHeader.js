import classNames from "classnames";
import { CouponURLs } from "./globals";
import { __ } from "./CouponURLs";

const CardHeader = ({component: {type, name}, onClose}) => {
    return <div className="flex flex-row items-center justify-between space-x-4">
                <div className="flex flex-row space-x-2 items-center">
                    <div className={classNames({
                        'flex rounded-5 p-1': true,
                        'bg-gray-350': true,
                    })}>
                        <div className={`children:w-4 children:h-4 text-gray-100`}>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" />
                            </svg>
                        </div>
                    </div>
                    <h1 className={classNames({
                        'text-2x': true,
                        'text-gray-800': true,
                    })}>{name}</h1>
                </div>
                <button 
                    className={classNames({
                        'text-transparent h-5 px-3 rounded-full': true,
                        'bg-gray-150 hover:bg-gray-350 hover:text-gray-100': true
                    })}
                    onClick={onClose.bind(this, type)}
                >
                    {__('remove', CouponURLs.textDomain)}
                </button>
            </div>;
}

export default CardHeader;