import { useDispatch } from "react-redux";
import { CouponURLs, __ } from "./globals";
import actions from "./actions/Actions";

const Enabled = () => {
    const dispatch = useDispatch();
    return <button className="absolute left-0 top-[10px] -translate-x-full text-gray-100 h-13 px-2 flex flex-col justify-center items-center hover:bg-gray-450 bg-[#6c90e0] rounded-l-3 group" onClick={() => dispatch(actions.disable())}>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" className="w-6 h-6 group-hover:hidden">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" className="w-6 h-6 hidden group-hover:block">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <p className="text-smaller-1 group-hover:hidden">{__('Enabled', CouponURLs.textDomain)}</p>
                <p className="text-smaller-1 hidden group-hover:block">{__('Disable?', CouponURLs.textDomain)}</p>
           </button>;
}

export default Enabled;