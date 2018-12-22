import { getFile } from '../../api'

export default {
    data () {
        return {
            detailModalOpen: false,
        }
    },

    methods: {
        openDetailModal () {
            this.detailModalOpen = true
        },

        closeDetailModal () {
            this.detailModalOpen = false
        },

        detailRequest () {
            return getFile(this.resourceName, this.resourceId)
        }
    }
}
