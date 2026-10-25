import { useSelector } from "react-redux";
import NoActions from "./NoActions";
import ActionsList from "./ActionsList";
import { ActionButtons } from "./ActionButtons";

const ActionsBox = () => {
    const currentActions = useSelector(state => state.actions);

    return <div className="w-full flex-row items-center justify-center mt-10">
                <div className="flex flex-col items-center text-gray-500">
                    <NoActions actions={currentActions}/>  
                    <ActionsList actions={currentActions} />
                    <ActionButtons actions={currentActions} />
                </div>
           </div>;
}

export default ActionsBox;