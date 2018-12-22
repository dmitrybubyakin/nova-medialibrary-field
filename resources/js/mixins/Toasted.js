export default {
    methods: {
        info (message, bindings = {}, options = {}) {
            this.toast(this.__(message, bindings), { type: 'success', ...options })
        },

        error (message, bindings = {}, options = {}) {
            this.toast(this.__(message, bindings), { type: 'error', ...options })
        },

        toast (message, options) {
            this.$toasted.show(message, options)
        }
    }
}
