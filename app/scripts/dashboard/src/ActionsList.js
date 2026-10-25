import { useDispatch } from "react-redux";
import CardFields from "./CardFields";
import CardHeader from "./CardHeader";
import { getActionComponentForType } from "./utils/actionHelpers";
import Actions from "./actions/Actions";
import { getIllustrationFor } from "./utils/IconHelpers";

const ActionsList = ({actions}) => {
    const dispatch = useDispatch();
    const line = <div className="w-px h-4 bg-gray-350"></div>

    return <div className="w-full flex flex-col items-center max-w-[400px]">
                {actions.map(({type, options}, index) => {
                    const actionComponent = getActionComponentForType(type);

                    return <>
                        {index > 0? line : ''}
                        <div class="flex flex-row items-center justify-center w-8 h-8 bg-gray-400 rounded-full text-gray-100">{index + 1}</div>
                        {line}
                        <div className="w-full min-w-65 bg-gray-100 relative rounded-3">
                            <div className={`relative w-full h-20 bg-gray-250 rounded-t-3 overflow-hidden coupon-urls-header-illustration ${type}`}>
                                {getIllustrationFor(type)}
                            </div>
                            <div className="space-y-4 w-full h-full p-3">
                                <CardHeader 
                                    component={actionComponent} 
                                    onClose={() => dispatch(Actions.removeAction(type))}
                                    />
                            </div>
                            <CardFields component={actionComponent} data={options}/>
                        </div>
                        {(actionComponent?.descriptions || [])?.map(description => (
                            <div className="w-full h-full mt-1 bg-gray-250 py-2 px-3 rounded-3 flex flex-row items-center space-x-1 text-gray-550">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="min-w-[18px] max-w-[18px] text-current">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <p className="text-smaller-1 leading-[15px]">{description}</p>
                        </div>
                        ))}
                        </>
                })}
           </div>;
}

export default ActionsList;