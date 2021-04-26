export default (config) => { return {
    open: false,
    focusedOptionIndex: null,
    wireModel: config.wireModel ?? null,

    focusIndex: function (index = null) {
        this.focusedOptionIndex = index;
    },

    focusNextOption: function () {
        if (this.focusedOptionIndex === null) {
            this.focusedOptionIndex = 0;
            this.scrollToFocusedOption();
            return;
        }

        if (this.focusedOptionIndex < this.$refs.select.length - 1) {
            this.focusedOptionIndex++;
            this.scrollToFocusedOption();
        }
    },

    focusPreviousOption: function () {
        if (this.focusedOptionIndex === null) {
            this.focusedOptionIndex = this.$refs.select.length - 1;
            this.scrollToFocusedOption();
            return;
        }

        if (this.focusedOptionIndex > 0) {
            this.focusedOptionIndex--;
            this.scrollToFocusedOption();
        }
    },

    selectOption: function (keepFocus = false) {
        if (! this.open)
            return this.toggleListbox();

        this.$refs.select.selectedIndex = this.focusedOptionIndex;

        // Livewire doesn't get the update event if the value was changed from JS,
        // so we have to manually send the value to its backend.
        if (this.wireModel)
            this.$wire.set(this.wireModel, this.$refs.select.value);

        this.closeListbox(keepFocus);
    },

    closeListbox: function (keepFocus = false) {
        this.open = false;
        this.focusedOptionIndex = null;

        if (keepFocus)
            this.$refs.button.focus();
    },

    toggleListbox: function () {
        if (this.open) {
            this.open = false;
            this.focusedOptionIndex = null;
            return;
        }

        this.open = true;
        this.focusedOptionIndex = this.$refs.select.selectedIndex;

        if (this.focusedOptionIndex < 0)
            this.focusedOptionIndex = 0;

        this.$nextTick(() => {
            // Keep the focus on the button
            this.$refs.button.focus();
            this.scrollToFocusedOption();
        });
    },

    scrollToFocusedOption: function () {
        // "+1" is because the first element in the DOM will be the <template>
        this.$refs.listbox.children[this.focusedOptionIndex + 1].scrollIntoView({
            block: 'start',
            behavior: 'smooth',
        });
    },
}}
