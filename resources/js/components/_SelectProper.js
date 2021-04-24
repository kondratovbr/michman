export default (config) => { return {
    providedOptions: config.options,
    //data: config.options,
    focusedOptionIndex: null,
    name: config.name,
    open: false,
    options: {},
    placeholder: config.placeholder ?? ' ',
    value: config.value,
    wireModel: config.wireModel ?? null,
    wireEvent: config.wireEvent ?? null,
    wireOptionsEvent: config.wireOptionsEvent ?? null,

    closeListbox: function (keepFocus = true) {
        this.open = false
        this.focusedOptionIndex = null
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
        // Set up the component to listen for value changes from the Livewire side
        if (this.wireModel != null) {
            // Update the value in Alpine when it is changed by Livewire
            if (this.wireEvent != null) {
                this.$wire.on(this.wireEvent, (value) => {
                    console.log('Handling value changed event from Livewire')
                    this.value = value
                })
            }
            // Re-initialize the component when Livewire changes the options provided
            if (this.wireOptionsEvent != null) {
                this.$wire.on(this.wireOptionsEvent, () => {
                    console.log('Re-initializing select, new data')
                    console.log(this.providedOptions)
                    this.options = this.providedOptions ?? []
                    this.value = null
                })
            }
        }

        this.options = this.providedOptions ?? []

        if (! (this.value in this.options))
            this.value = null
    },

    selectOption: function (value = null) {
        if (! this.open) {
            this.toggleListboxVisibility()
            return
        }

        this.value = Object.keys(this.options)[this.focusedOptionIndex]

        // This integrates the component with Livewire
        if (this.wireModel != null)
            this.$wire.set(this.wireModel, this.value)

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
            this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                block: "start",
                behavior: "smooth"
            })
        })
    },
}}
