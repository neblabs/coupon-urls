import { useDispatch } from "react-redux";
import { TipMenu } from "./TipMenu";
import { CouponURLs } from "./globals";
import actions from "./actions/Actions";
import { __, components } from "./CouponURLs";

export const ActionButtons = ({actions: currentActions}) => {
    const dispatch = useDispatch();

    return <div className="flex items-center justify-center space-x-2 mt-4">
        <TipMenu button={
                    <button className={"flex flex-row space-x-1 items-center justify-center bg-blue-normal text-gray-100 px-8 h-8 rounded-1"}>
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                        </svg>
                        <span className="flex h-4">{__('Add action', CouponURLs.textDomain)}</span>
                    </button>
                } 
                components={components.actions}
                selectedIds={currentActions.map(({type}) => type)}
                idKey="type"
                onSelection={id => dispatch(actions.addAction(id))}
        />
        {!currentActions.length? <>
            <span className="text-smaller-1">{__('or')}</span>
            <button className="flex flex-row space-x-1 items-center justify-center bg-gray-300 bg-opacity-70 text-gray-650 px-6 h-8 rounded-1"
                    onClick={() => dispatch(actions.setActions(JSON.parse('[{"type":"AddCoupon","options":[]},{"type":"Redirection","options":{"type":"cart","value":""}}]')))}
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="min-w-4 max-w-4 min-h-4 max-h-4">
                  <path fillRule="evenodd" d="M3 6a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3V6ZM3 15.75a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-2.25Zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3v-2.25Z" clipRule="evenodd" />
                </svg>
                <span>{__('Simple add & redirect')}</span>
            </button>
        </> : null}
    </div>
}