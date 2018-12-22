import { deleteFile } from '../../api'

export default {
    data () {
        return {
            deleteModalOpen: false,
        }
    },

    computed: {
        deleteModalClosed () {
            return ! this.deleteModalOpen
        }
    },

    methods: {
        openDeleteModal () {
            this.deleteModalOpen = true
        },

        closeDeleteModal () {
            this.deleteModalOpen = false
        },

        async deleteResource () {
            try {
                await deleteFile(this.resourceName, this.resourceId)

                this.info('The :resource was deleted!', { resource: this.resourceName })

                this.resourceDeleted()
            } catch (error) {
                this.requestFailed(error)
            }
        }
    }
}
