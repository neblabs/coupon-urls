import { useContext } from "react"
import { __ } from "./CouponURLs"
import { DashboardManagerContext } from "./contexts"

const backToClassic = (dashboardManager) => {
    dashboardManager.close()
}
export const Header = ({show}) => {
    const dashboardManager = useContext(DashboardManagerContext)

    if (!show) {
        return
    }

    return <div className="w-full h-12 flex items-center justify-between gap-3">
        <button className="flex items-center gap-1 text-gray-550 px-3 py-1 rounded-full bg-gray-800/5" onClick={backToClassic.bind(this, dashboardManager)}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="min-w-4 max-w-4 min-h-4 max-h-4">
            <path fillRule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-4.28 9.22a.75.75 0 0 0 0 1.06l3 3a.75.75 0 1 0 1.06-1.06l-1.72-1.72h5.69a.75.75 0 0 0 0-1.5h-5.69l1.72-1.72a.75.75 0 0 0-1.06-1.06l-3 3Z" clipRule="evenodd" />
            </svg>
            <span className="">{__('Back')}</span>
        </button>
        <div className="">the save</div>
    </div>
}
