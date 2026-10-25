import { useSelector } from "react-redux";
import ActionsBox from "./ActionsBox";
import URI from "./URI";
import Disabled from "./Disabled";

const Dashboard = () => {
    const isEnabled = useSelector(state => state.options.isEnabled)

    return <div className="flex flex-col items-center w-full">
                {isEnabled? (
                    <>
                        <URI />
                        <ActionsBox />
                    </>
                ): (
                    <Disabled />
                )}
           </div>;
}

export default Dashboard;