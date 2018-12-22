export default {
    data () {
        return {
            uploadedFiles: [],
        }
    },

    mounted () {
        const event = `medialibrary:uploaded-to-${this.field.collectionName}`

        const listener = file => this.uploadedFiles.push(file)

        Nova.$on(event, listener)

        this.$once('hook:beforeDestroy', () => Nova.$off(event, listener))
    }
}
