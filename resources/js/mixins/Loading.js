export default {
    data () {
        return {
            loadingFileId: null,
        }
    },

    computed: {
        showActions () {
            return ! this.loadingFileId
        }
    },

    mounted () {
        const loadingStartedListener = fileId => this.loadingFileId = fileId
        const loadingFinishedListener = () => this.loadingFileId = null

        Nova.$on('medialibrary:loading-started', loadingStartedListener)
        Nova.$on('medialibrary:loading-finished', loadingFinishedListener)

        this.$once('hook:beforeDestroy', () => {
            Nova.$off('medialibrary:loading-started', loadingStartedListener)
            Nova.$off('medialibrary:loading-finished', loadingFinishedListener)
        })
    }
}
