export default (config) => { return {
    data: config.data,
    emptyOptionsMessage: config.emptyOptionsMessage ?? 'No results match your search.',
    focusedOptionIndex: null,
    name: config.name,
    open: false,
    options: {},
    placeholder: config.placeholder ?? 'Select an option',
    search: '',
    value: config.value,

    closeListbox: function (keepFocus = true) {
        this.open = false
        this.focusedOptionIndex = null
        // If we do it immediately then you can notice
        // how search box clears before value/placeholder is shown.
        setTimeout(() => {
            this.search = ''
        }, 150)
        // Keep focus on the activation button after the list is closed.
        if (keepFocus)
            this.$nextTick(() => {
                this.$refs.button.focus()
            })
    },

    focusNextOption: function () {
        if (this.focusedOptionIndex === null) {
            this.focusedOptionIndex = 0
        } else if (this.focusedOptionIndex + 1 < Object.keys(this.options).length) {
            this.focusedOptionIndex++
        }

        this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
            block: "start",
            behavior: "smooth"
        })
    },

    focusPreviousOption: function () {
        if (this.focusedOptionIndex === null) {
            this.focusedOptionIndex = Object.keys(this.options).length - 1
            return
        }

        if (this.focusedOptionIndex <= 0)
            return

        this.focusedOptionIndex--

        this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
            block: "start",
            behavior: "smooth"
        })
    },

    init: function () {
        this.options = this.data ?? []

        if (! (this.value in this.options))
            this.value = null

        this.$watch('search', ((value) => {
            if (!this.open || !value)
                return this.options = this.data

            this.options = Object.keys(this.data)
                .filter((key) => this.data[key].toLowerCase().includes(value.toLowerCase()))
                .reduce((options, key) => {
                    options[key] = this.data[key]
                    return options
                }, {})
        }))
    },

    selectOption: function () {
        if (! this.open)
            return this.toggleListboxVisibility()

        this.value = Object.keys(this.options)[this.focusedOptionIndex]
        this.closeListbox(false)
    },

    toggleListboxVisibility: function () {
        if (this.open) {
            this.closeListbox()
            return
        }

        this.focusedOptionIndex = Object.keys(this.options).indexOf(this.value)

        if (this.focusedOptionIndex < 0)
            this.focusedOptionIndex = 0

        this.open = true

        this.$nextTick(() => {
            this.$refs.search.focus()
            this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                block: "start",
                behavior: "smooth"
            })
        })
    },
}}
