import { useContext, useState } from "react"
import { __ } from "./CouponURLs"
import { DashboardManagerContext } from "./contexts"
import classNames from "classnames"

const backToClassic = (dashboardManager) => {
    dashboardManager.close()
}
export const Header = ({show}) => {
    const dashboardManager = useContext(DashboardManagerContext)
    const [isSaving, setIsSaving] = useState(false)

    const save = () => {
        setIsSaving(true)
        // let's show the saving state in the button before we actually save, just a nice ui feedback.
        window.setTimeout(() => {
            $('#publish').click()
        }, 500)
    }
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
        <button className={classNames("flex items-center gap-1 text-gray-100/90 px-3 py-1 rounded-full", {
            'bg-blue-normal': !isSaving,
            'bg-gray-600': isSaving
        })} onClick={save}>
            <span className="">{isSaving ? __('Saving...') : __('Save')}</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="min-w-4 max-w-4 min-h-4 max-h-4">
                <path fillRule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clipRule="evenodd" />
            </svg>
        </button>
    </div>
}
