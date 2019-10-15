import { getFile } from '../../api'

export default {
    data () {
        return {
            detailModalOpen: false,
            detailModalWasOpen: false,
        }
    },

    methods: {
        openDetailModal () {
            this.detailModalOpen = true
        },

        closeDetailModal () {
            this.detailModalOpen = false
        },

        swapDetailModal (callback = null) {
            [
                this.detailModalOpen,
                this.detailModalWasOpen,
            ] = [
                this.detailModalWasOpen,
                this.detailModalOpen,
            ]

            callback && this.$nextTick(callback)
        },

        detailRequest () {
            return getFile(this.resourceName, this.resourceId)
        }
    }
}
