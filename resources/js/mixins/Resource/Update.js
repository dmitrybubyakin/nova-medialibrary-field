import { getUpdateFields, updateFile } from '../../api'
import { Errors } from 'laravel-nova'

export default {
    data () {
        return {
            processing: false,
            updateModalOpen: false,
            validationErrors: new Errors(),
        }
    },

    methods: {
        openUpdateModal () {
            this.closeDetailModal()

            this.updateModalOpen = true
        },

        closeUpdateModal () {
            this.updateModalOpen = false
        },

        updateFieldsRequest () {
            return getUpdateFields(this.resourceName, this.resourceId)
        },

        async updateResource (formData) {
            this.processing = true

            try {
                await updateFile(this.resourceName, this.resourceId, formData)

                this.resourceUpdated()

                this.info('The :resource was updated!', { resource: this.resourceName })
            } catch (error) {
                this.handleUpdateRequestError(error)
            }

            this.processing = false
        },

        handleUpdateRequestError ({ response }) {
            if (response.status === 422) {
                this.validationErrors = new Errors(response.data.errors)
            } else if (response.status === 409) {
                this.error('Another user has updated this resource since this page was loaded. Please refresh the page and try again.')
            } else {
                this.requestFailed({ response } )
            }
        }
    }
}
