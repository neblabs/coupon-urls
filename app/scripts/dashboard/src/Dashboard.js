import { useSelector } from "react-redux";
import ActionsBox from "./ActionsBox";
import URI from "./URI";
import Disabled from "./Disabled";
import { Header } from "./header.jsx";
import classNames from "classnames";

const Dashboard = () => {
    const isEnabled = useSelector(state => state.options.isEnabled)
    const showHeader = true
    return <div className={classNames("flex flex-col items-center w-full", {
        'p-4': !showHeader,
        'pb-4 px-4': showHeader
    })}>
        <Header show={showHeader} />
        {isEnabled ? (
            <>
                <URI />
                <ActionsBox />
            </>
        ) : (
            <Disabled />
        )}
    </div>;
}

export default Dashboard;
