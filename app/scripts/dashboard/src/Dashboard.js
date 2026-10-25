import { useSelector } from "react-redux";
import ActionsBox from "./ActionsBox";
import URI from "./URI";
import Disabled from "./Disabled";
import { Header } from "./header.jsx";
import classNames from "classnames";
import { useContext, useState, useEffect } from "react";
import { DashboardManagerContext } from "./contexts.js";

const Dashboard = () => {
    const isEnabled = useSelector(state => state.options.isEnabled);
    const manager = useContext(DashboardManagerContext);

    const [isFullCanvas, setIsFullCanvas] = useState(manager?.isFullCanvas ?? false);

    useEffect(() => {
        if (manager) {
            setIsFullCanvas(manager.isFullCanvas);
        }

        const handleConfigUpdate = (e) => {
            const nextValue = e?.detail?.isFullCanvas ?? manager?.isFullCanvas;
            if (typeof nextValue !== 'undefined') {
                setIsFullCanvas(Boolean(nextValue));
            }
        };

        window.addEventListener('couponURLs:config:update', handleConfigUpdate);
        return () => {
            window.removeEventListener('couponURLs:config:update', handleConfigUpdate);
        };
    }, [manager]);

    const showHeader = isFullCanvas;

    return (
        <div className={classNames("flex flex-col items-center w-full", {
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
        </div>
    );
};

export default Dashboard;
