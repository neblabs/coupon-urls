import { textDomain } from "./CouponURLs";
import { security } from "./CouponURLs";
import { features } from "./CouponURLs";
import { __ } from "./globals";

class DashboardManager {
    state = {
        isOpen: false
    }

    fieldElements = {}

    beforeSaveEvents = [
        this.validate,
        this.updateStateToFormElements
    ]
    saveValidators = [
        this.noActionsValidator,
        this.noMainPathValidator,
    ]

    /**
     * @param {Object} store
     * @param {Object|boolean} [options={ fullCanvas: false }] - Flag or options object
     */
    constructor(store, options = {}) {
        this.store = store

        if (typeof options === 'boolean') {
            this.options = { fullCanvas: options }
        } else {
            this.options = options || {}
        }

        this.isFullCanvas = Boolean(
            this.options.fullCanvas ?? features?.fullCanvas ?? false
        )

        this.createElements()
        this.createDashboardOpenCloseEvent()
        this.createSaveEvents()
        this.setupResizeListeners()
        this.registerInitEvent()
    }

    open() 
    {
        this.beforeOpen()

        if (!this.state.isOpen) {
            this.state.isOpen = true;
            $(this.getMainElement()).removeClass('hidden')
            this.getClassicPanelContentElement().css('display', 'none')

            if (this.isFullCanvas) {
                this.getWooHeaderElement().css('display', 'none')
                this.getWpFooterElement().css('display', 'none')

                // Update height immediately and on the next animation frame
                this.updateCanvasHeight()
                requestAnimationFrame(() => this.updateCanvasHeight())
            }
        }
    }

    close() 
    {
        this.beforeClose()
        if (this.state.isOpen) {
            this.state.isOpen = false;
            $(this.getMainElement()).addClass('hidden')
        }
    }

    getMainElement() 
    {
        return document.getElementById('coupon_urls')
    }

    getClassicPanelContentElement() 
    {
        const contentElement = $('#wpbody-content .wrap, #wpbody-content > form#post');

        if (typeof this.classicPanelContentElementOriginalDisplayValue === 'undefined') {
            this.classicPanelContentElementOriginalDisplayValue = contentElement.css('display')
        }

        return contentElement
    }

    getWooHeaderElement() 
    {
        const headerElement = $('.woocommerce-layout__header, .woocommerce-layout__header-wrapper');

        if (typeof this.wooHeaderElementOriginalDisplayValue === 'undefined' && headerElement.length) {
            this.wooHeaderElementOriginalDisplayValue = headerElement.css('display')
        }

        return headerElement
    }

    getWpFooterElement() {
        const footerElement = $('#wpfooter');

        if (typeof this.wpFooterElementOriginalDisplayValue === 'undefined' && footerElement.length) {
            this.wpFooterElementOriginalDisplayValue = footerElement.css('display')
        }

        return footerElement
    }

    getContainerElement() 
    {
        if (this.isFullCanvas) {
            const wpcontent = $('#wpcontent');
            if (wpcontent.length) {
                wpcontent.css('position', 'relative');
                return wpcontent;
            }
        }

        return $('#wpbody-content');
    }

    /**
     * Ensures the dashboard height covers at least all scrollable height
     * (viewport, left sidebar menu, and document scroll height) and grows as content grows.
     */
    updateCanvasHeight() {
        if (!this.isFullCanvas || !this.state.isOpen) {
            return;
        }

        const mainElement = this.getMainElement();
        if (!mainElement) {
            return;
        }

        const wpAdminBar = document.getElementById('wpadminbar');
        const adminBarHeight = wpAdminBar ? wpAdminBar.offsetHeight : 32;

        // 1. Minimum height needed to cover the visible viewport canvas
        const viewportCanvasHeight = window.innerHeight - adminBarHeight;

        // 2. Height of the WordPress left admin menu
        const adminMenu = document.getElementById('adminmenuwrap') || document.getElementById('adminmenu');
        const adminMenuHeight = adminMenu ? adminMenu.offsetHeight : 0;

        // Temporarily reset min-height to accurately measure underlying scroll height
        mainElement.style.minHeight = '';
        const wpContent = document.getElementById('wpcontent');
        if (wpContent) {
            wpContent.style.minHeight = '';
        }

        // 3. Document scroll height
        const docScrollHeight = Math.max(
            document.documentElement.scrollHeight - adminBarHeight,
            document.body.scrollHeight - adminBarHeight
        );

        // 4. Element's own content height
        const contentHeight = mainElement.scrollHeight;

        // Take the maximum of all dimensions
        const targetMinHeight = Math.max(
            viewportCanvasHeight,
            adminMenuHeight,
            docScrollHeight,
            contentHeight
        );

        mainElement.style.minHeight = `${targetMinHeight}px`;

        if (wpContent) {
            wpContent.style.minHeight = `${targetMinHeight}px`;
        }
    }

    setupResizeListeners() {
        if (!this.isFullCanvas) {
            return;
        }

        window.addEventListener('resize', () => {
            if (this.state.isOpen) {
                this.updateCanvasHeight();
            }
        });

        // ResizeObserver watches for DOM changes/growth inside the dashboard content
        if (typeof ResizeObserver !== 'undefined') {
            const mainElement = this.getMainElement();
            if (mainElement) {
                this.resizeObserver = new ResizeObserver(() => {
                    if (this.state.isOpen) {
                        this.updateCanvasHeight();
                    }
                });
                this.resizeObserver.observe(mainElement);
            }
        }
    }

    addDashboardButtons() 
    {
        window.document.body.classList.add('coupon_urls')

        const urlsButton = `<button class="coupon_urls--open-button h-[30px] inline-flex flex-row items-center justify-center px-2 bg-[#6c90e0] text-gray-100 rounded-4 space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="min-w-4 max-w-4 min-h-4 max-h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643m5.276-3.67a9.012 9.012 0 01-5.276 3.67m0 0a9 9 0 01-10.275-4.835M15.75 9c0 .896-.393 1.7-1.016 2.25"></path></svg>
            <span>Configure URL</span>
        </button>`

        if (features.buttons.dashboardLauncher) {
            $('#titlewrap').append(urlsButton)
        }

        //this.addBackToClassicElement()
    }

    getHeadingRoot() {
        return $('.woocommerce-layout__header-heading--coupons-plus')
    }

    createElements() {
        // woo changed the heading markup in newer versions so lets re-add it ourselves
        if (!this.getHeadingRoot().length) {
            $('.wrap').before('<div class="woocommerce-layout__header-heading--coupons-plus h-0 flex items-center w-full --bg-[#fff]">')
        }

        this.addDashboardButtons()

        const urlParams = new URLSearchParams(window.location.search);
        const openedByDefault = urlParams.get('coupon-urls-opened') === 'true';

        const fullCanvasClass = this.isFullCanvas ? 'coupon_urls--full-canvas' : '';
        const targetContainer = this.getContainerElement();

        targetContainer.append(`<div id="coupon_urls" class="${openedByDefault || 'hidden'} ${fullCanvasClass} left-0 top-0 z-[1000] w-full relative bg-gray-200"><div>`)

        const form = document.querySelector('form#post')

        if (form) {
            const fields = [
                'coupon_urls_state',
                'coupon_urls_dashboard_nonce'
            ]

            fields.forEach(field => {
                const fieldElement = document.createElement('input')

                fieldElement.type = 'hidden'
                fieldElement.id = field
                fieldElement.name = field

                form.append(fieldElement)
                this.fieldElements[field] = fieldElement
            });
        }

        // Open properly on load if opened by query param
        if (openedByDefault) {
            setTimeout(() => this.open(), 0);
        }
    }

    createDashboardOpenCloseEvent() 
    {
        $('.coupon_urls--open-button').on('click', (event) => {
            event.preventDefault()
            this.open()
        })
    }

    createSaveEvents() {
        const publishButton = document.querySelector('#publish.button');
        if (!publishButton) {
            return;
        }

        publishButton.addEventListener('click', (event) => {
            this.beforeSaveEvents
                .map(runner => runner.bind(this, event))
                .forEach(runner => runner())
        }, true);
    }

    registerInitEvent() 
    {
        window.addEventListener(
            'load', 
            () => document.dispatchEvent(new CustomEvent('CouponURLsInit', {detail: {manager: this}}))
        )
    }

    beforeOpen() 
    {
    }

    beforeClose() 
    {
        this.getClassicPanelContentElement().css('display', this.classicPanelContentElementOriginalDisplayValue)

        if (this.isFullCanvas) {
            this.getWooHeaderElement().css('display', this.wooHeaderElementOriginalDisplayValue ?? '')
            this.getWpFooterElement().css('display', this.wpFooterElementOriginalDisplayValue ?? '')

            // Clean up inline min-heights when returning to classic view
            const mainElement = this.getMainElement();
            if (mainElement) {
                mainElement.style.minHeight = '';
            }
            const wpcontent = document.getElementById('wpcontent');
            if (wpcontent) {
                wpcontent.style.minHeight = '';
            }
        }
    }

    validate(event) 
    {
        if (!this.store.getState().options.isEnabled) {
            return
        }
        
        const errorMessage = this.runValidators()

        if (errorMessage) {
            event.preventDefault()
            event.stopPropagation()
            event.stopImmediatePropagation()
            alert(errorMessage)
        }
    }   

    runValidators() 
    {
        let errorMessage = null;

        this.saveValidators.map(validator => validator.bind(this))
                           .find(validator => errorMessage = validator())

        return errorMessage
    }

    noMainPathValidator() 
    {
        if (this.store.getState().uri.type === 'path' && !this.store.getState().uri.value.replace('/', '')) {
            return __('You need to select a path - Coupon URLs')
        }
    }

    noActionsValidator() 
    {
        if (!this.store.getState().actions.length) {
            return __('You need to add one or more actions - Coupon URLs')
        }
    }

    updateStateToFormElements(event) 
    {
        this.fieldElements['coupon_urls_state'].value = JSON.stringify(this.store.getState())
        this.fieldElements['coupon_urls_dashboard_nonce'].value = security.nonce.value
    }
}

export default DashboardManager;
