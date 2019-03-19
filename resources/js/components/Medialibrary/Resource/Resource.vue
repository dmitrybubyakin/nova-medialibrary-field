<template>
    <div>
        <slot :file="file" :file-events="fileEvents"/>

        <portal to="modals">
            <transition name="fade">
                <Request v-if="detailModalOpen" :request="detailRequest" v-on="requestEvents">
                    <DetailModal v-if="deleteModalClosed" slot-scope="{ resource }"
                        :resource-name="resourceName"
                        :resource-id="resourceId"
                        :resource="resource"
                        :readonly="readonly"
                        @close="closeDetailModal"
                        @update="openUpdateModal"
                        @delete="openDeleteModal"
                    />
                </Request>
            </transition>
        </portal>

        <portal to="modals">
            <transition name="fade">
                <Request v-if="updateModalOpen" :request="updateFieldsRequest" v-on="requestEvents">
                    <UpdateModal slot-scope="fields"
                        :resource-name="resourceName"
                        :resource-id="resourceId"
                        :fields="fields"
                        :validation-errors="validationErrors"
                        :processing="processing"
                        @submit="updateResource"
                        @close="closeUpdateModal"
                    />
                </Request>
            </transition>
        </portal>

        <portal to="modals">
            <transition name="fade">
                <delete-resource-modal v-if="deleteModalOpen"
                    @confirm="deleteResource"
                    @close="closeDeleteModal"
                    mode="delete"
                />
            </transition>
        </portal>
    </div>
</template>

<script>
import { Detail, Update, Delete } from '../../../mixins/Resource'
import { Toasted } from '../../../mixins'
import Request from './Request'
import DetailModal from '../Modal/DetailModal'
import UpdateModal from '../Modal/UpdateModal'

export default {
    props: {
        file: Object,
        field: Object,
        readonly: Boolean,
    },

    mixins: [Detail, Update, Delete, Toasted],

    components: { Request, DetailModal, UpdateModal },

    computed: {
        fileEvents () {
            return {
                view: this.openDetailModal,
                update: this.openUpdateModal,
                delete: this.openDeleteModal,
            }
        },

        requestEvents () {
            return {
                started: this.requestStarted,
                finished: this.requestFinished,
                failed: this.requestFailed,
            }
        },

        resourceName () {
            return this.field.resourceName
        },

        resourceId () {
            return this.file.id
        }
    },

    methods: {
        requestStarted () {
            Nova.$emit('medialibrary:loading-started', this.file.id)
        },

        requestFinished () {
            Nova.$emit('medialibrary:loading-finished')
        },

        requestFailed ({ response }) {
            this.closeDetailModal()
            this.closeUpdateModal()
            this.closeDeleteModal()

            if (response.status === 404) {
                this.error('This resource no longer exists')
            }
        },

        resourceUpdated () {
            this.closeUpdateModal()
            this.refreshFields()
        },

        resourceDeleted () {
            this.closeDeleteModal()
            this.closeDetailModal()
            this.refreshFields()
        },

        async refreshFields () {
            const { resourceName, resourceId } = this.$route.params

            const { data: { resource: { fields }}} = await Nova.request().get(`/nova-api/${resourceName}/${resourceId}`)

            _.toArray(fields).filter(field => field.component === 'nova-medialibrary-field').forEach(field => {
                Nova.$emit(`medialibrary:field-${field.collectionName}-updated`, field)
            })
        }
    }
}
</script>
