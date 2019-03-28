<script>
export default {
    props: {
        field: Object,
        file: Object,
        fileEvents: Object,
    },

    render () {
        return this.$scopedSlots.default({
            file: this.file,
            fileEvents: this.fileEvents,
        })
    }
}
</script>
