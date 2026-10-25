import QueryParameter from "./QueryParameter";
import { useDispatch, useSelector } from "react-redux";
import actions from "./actions/Actions";
import { __, textDomain } from "./CouponURLs";

const QueryParameters = () => {
    const queryParameters= useSelector(store => store.queryParameters);
    const dispatch = useDispatch();

    const And = <span className="text-3x text-gray-350">&</span>;

    return  <p className="flex flex-col items-start justify-center mt-0">
                <p className="text-smaller-2 text-gray-300">{
                    !queryParameters.length? __('Any Query parameters', textDomain) : __('MUST have these query parameters:', textDomain)
                }</p>
                <div className="flex flex-row items-center">
                    <span className="text-3x text-gray-350">?</span>
                    {queryParameters.map(({key, value}, index) => <>
                        <QueryParameter key={key+''+index} parameterKey={key} parameterValue={value} parameterIndex={index}/>
                        {And}
                    </>)}
                    <button className={"bg-gray-300 text-gray-100 p-[2px] rounded-full ml-1" } onClick={() => dispatch(actions.newQueryParameter())}>
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                          <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                        </svg>
                    </button>
                </div>
            </p>;
}

export default QueryParameters;