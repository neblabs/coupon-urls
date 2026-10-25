import { useState } from "react";
import { useDispatch } from "react-redux";
import actions from "./actions/Actions";

const QueryParameter = ({parameterKey, parameterValue = '', parameterIndex}) => {
    const [key, setkey] = useState(parameterKey);
    const [value, setvalue] = useState(parameterValue);
    const dispatch = useDispatch();

    return <div className="flex flex-row items-center relative group">
                <div className="absolute top-0 -translate-y-full w-full flex flex-col items-end invisible group-hover:visible">
                    <button class="bg-gray-250 rounded-full" onClick={dispatch.bind(this, actions.deleteQueryParameter(parameterIndex))}><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 opacity-0 group-hover:opacity-20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></button>
                </div>
                <input type="text" className="w-20 border-none bg-transparent text-gray-650" value={key} onChange={(event) => setkey(event.target.value)} onBlur={() => dispatch(actions.updateQueryParameter('key', key, parameterIndex))}/>
                <div className="text-3x text-gray-350">=</div>
                <input type="text" className="w-20 border-none bg-transparent text-gray-650" value={value} onChange={event => setvalue(event.target.value)} onBlur={() => dispatch(actions.updateQueryParameter('value', value, parameterIndex))} />
           </div>;
}

export default QueryParameter;