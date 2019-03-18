<template>
    <modal class="select-text" @modal-close="handleClose">
        <card class="w-action-fields overflow-hidden">
            <div class="px-8 py-6">
                <div class="flex items-center">
                    <h4 class="text-90 font-normal text-2xl flex-no-shrink">{{ resource.id.panel }}</h4>

                    <div class="ml-3 w-full flex items-center justify-end">
                        <button v-if="authorizedToDelete"
                            class="btn btn-default btn-icon btn-white"
                            @click="handleDelete"
                            :title="__('Delete')"
                        >
                            <icon type="delete" class="text-80"/>
                        </button>

                        <button v-if="authorizedToUpdate"
                            class="btn btn-default btn-icon bg-primary ml-3"
                            @click="handleUpdate"
                            :title="__('Edit')"
                        >
                            <icon type="edit" class="text-white" style="margin-top: -2px; margin-left: 3px"/>
                        </button>
                    </div>
                </div>

                <component
                    :class="{'remove-bottom-border': index == resource.fields.length - 1}"
                    :key="index"
                    v-for="(field, index) in resource.fields"
                    :is="resolveComponentName(field)"
                    :resource-name="resourceName"
                    :resource-id="resourceId"
                    :resource="resource"
                    :field="field"
                />
            </div>
        </card>
    </modal>
</template>

<script>

export default {
    props: {
        resourceName: String,
        resourceId: Number,
        resource: Object,
        readonly: Boolean,
    },

    computed: {
        authorizedToDelete () {
            return this.resource.authorizedToDelete && ! this.readonly
        },

        authorizedToUpdate () {
            return this.resource.authorizedToUpdate && ! this.readonly
        },
    },

    methods: {
        resolveComponentName(field) {
            return field.prefixComponent ? 'detail-' + field.component : field.component
        },

        handleClose () {
            this.$emit('close')
        },

        handleDelete () {
            this.$emit('delete')
        },

        handleUpdate () {
            this.$emit('update')
        }
    }
}
</script>
