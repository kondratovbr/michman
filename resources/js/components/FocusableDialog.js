// This component contains functions that handle focus changes (Tab button),
// so that when modal is shown focus is kept inside the modal.
export default () => { return {

    /**
     * Initialize the Alpine component.
     */
    init() {
        /**
         * Return a list of currently focusable elements in this component.
         */
        this.focusables = () => {
            // List all types of focusable elements,...
            let selector = 'a, button, input, textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            // ...take them filtering out the disabled ones.
            return [...this.$el.querySelectorAll(selector)].filter(el => !el.hasAttribute('disabled'));
        };

        /**
         * Return the first focusable element in this component.
         */
        this.firstFocusable = () => {
            return this.focusables()[0];
        };

        /**
         * Return the last focusable element in this component.
         */
        this.lastFocusable = () => {
            return this.focusables().slice(-1)[0];
        };

        /**
         * Return the next focusable element in this component.
         */
        this.nextFocusable = () => {
            // If there's no next focusable - return the first one.
            return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable();
        };

        /**
         * Return the previous focusable element in this component.
         */
        this.prevFocusable = () => {
            // If there's no previous focusable - return the last one.
            return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable();
        };

        /**
         * Return the index of the next focusable element in this component.
         */
        this.nextFocusableIndex = () => {
            return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1);
        };

        /**
         * Return the index of the previous focusable element in this component.
         */
        this.prevFocusableIndex = () => {
            return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1
        };
    },

}}
