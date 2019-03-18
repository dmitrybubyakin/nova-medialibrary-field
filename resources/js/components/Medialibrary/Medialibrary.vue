<template>
    <div>
        <SortFiles :files="sortedFiles" @sort="handleSort" :disabled="sortingDisabled" class="flex flex-wrap -m-2">
            <Resource v-for="file in sortedFiles" :key="file.id" class="p-2"
                :file="file"
                :field="field"
                :readonly="readonly"
                @refresh="refresh"
            >
                <File slot-scope="{ file, fileEvents }"
                    :file="file"
                    :width="field.thumbnailWidth"
                    :height="field.thumbnailHeight"
                    :loading="loadingFileId === file.id"
                    :show-actions="showActions"
                    v-on="fileEvents"
                />
            </Resource>
        </SortFiles>

        <template v-if="! readonly">
            <div :class="files.length ? 'border-b border-40 my-4' : 'my-2'"></div>

            <UploadFiles :field="field" @refresh="refresh"/>
        </template>
    </div>
</template>

<script>
import { Sortable, Toasted, Loading } from '../../mixins'
import SortFiles from './SortFiles'
import UploadFiles from './UploadFiles'
import Resource from './Resource/Resource'
import File from './File/File'

export default {
    mixins: [Sortable, Toasted, Loading],

    components: {
        SortFiles,
        UploadFiles,
        Resource,
        File,
    },

    props: {
        field: Object,
        readonly: Boolean,
    },

    data () {
        return {
            fieldFiles: [],
        }
    },

    computed: {
        files () {
            return this.field.multiple ? this.fieldFiles : this.fieldFiles.slice(-1)
        }
    },

    watch: {
        field: {
            immediate: true,
            handler (field) {
                this.handleFieldUpdate(field)
            }
        }
    },

    created () {
        const event = `medialibrary:field-${this.field.attribute}-updated`

        Nova.$on(event, this.handleFieldUpdate)

        this.$once('hook:beforeDestroy', () => Nova.$off(event, this.handleFieldUpdate))
    },

    methods: {
        handleFieldUpdate ({ value }) {
            this.fieldFiles = (value || []).map(file => {
                file.authorizedToDelete = !this.readonly && file.authorizedToDelete
                file.authorizedToUpdate = !this.readonly && file.authorizedToUpdate

                return file
            })
        },

        async refresh () {
            const { resourceName, resourceId } = this.$route.params

            const { data: { resource: { fields }}} = await Nova.request().get(`/nova-api/${resourceName}/${resourceId}`)

            fields.filter(field => field.component === 'nova-medialibrary-field').forEach(field => {
                Nova.$emit(`medialibrary:field-${field.attribute}-updated`, field)
            })
        }
    }
}
</script>
