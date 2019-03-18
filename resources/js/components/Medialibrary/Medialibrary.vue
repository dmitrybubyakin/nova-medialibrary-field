<template>
    <div>

        <SortFiles :files="sortedFiles" @sort="handleSort" :disabled="sortingDisabled" class="flex flex-wrap -m-2">
            <Resource v-for="file in sortedFiles" :file="file" :key="file.id" :field="field" class="p-2" @refresh="refresh">
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

        <div v-if="files.length" class="border-b border-40 my-4"></div>
        <div v-else class="my-2"></div>

        <UploadFiles :field="field" @refresh="refresh"/>
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
            this.fieldFiles = value || []
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
