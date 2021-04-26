export default (config) => { return {
    open: false,
    focusedOptionIndex: null,
    search: null,
    wireModel: config.wireModel ?? null,

    focusIndex: function (index = null) {
        this.focusedOptionIndex = index;
    },

    focusNextOption: function () {
        if (this.focusedOptionIndex === this.$refs.select.options.length - 1)
            return this.scrollToFocusedOption();

        let newIndex = this.focusedOptionIndex ?? -1;

        do {
            newIndex++;
        } while ( ! this.optionShown(newIndex) && newIndex < this.$refs.select.length - 1);

        if (this.optionShown(newIndex))
            this.focusedOptionIndex = newIndex;

        this.scrollToFocusedOption();
    },

    focusPreviousOption: function () {
        if (this.focusedOptionIndex === 0)
            return this.scrollToFocusedOption();

        let newIndex = this.focusedOptionIndex ?? this.$refs.select.options.length;

        do {
            newIndex--;
        } while ( ! this.optionShown(newIndex) && newIndex > 0);

        if (this.optionShown(newIndex))
            this.focusedOptionIndex = newIndex;

        this.scrollToFocusedOption();
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
            this.$refs.button.focus()
    },

    toggleListbox: function () {
        if (this.open)
            this.closeListbox(false);

        this.open = true;
        this.focusedOptionIndex = this.$refs.select.selectedIndex;

        if (this.focusedOptionIndex < 0)
            this.focusedOptionIndex = 0;

        this.$nextTick(() => {
            this.$refs.search.focus();
            this.scrollToFocusedOption();
        });
    },

    optionShown: function (index) {
        return this.search === null || this.$refs.select.options[index].text.toLowerCase().includes(this.search.toLowerCase());
    },

    scrollToFocusedOption: function () {
        // "+1" is because the first element in the DOM will be the <template>
        const topPos = this.$refs.listbox.children[this.focusedOptionIndex + 1].offsetTop;
        this.$refs.listbox.scroll({ top: topPos, behavior: 'smooth' });
    },
}}
