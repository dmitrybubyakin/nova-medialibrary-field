<template>
    <div :class="{ 'big-thumbnails': field.bigThumbnails }">

        <SortFiles :files="sortedFiles" @sort="handleSort" :disabled="sortingDisabled" class="flex flex-wrap -m-2">
            <Resource v-for="file in sortedFiles" :file="file" :key="file.id" :field="field" class="p-2">
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

        <UploadFiles :field="field"/>
    </div>
</template>

<script>
import { Sortable, Toasted, Loading, UploadedFiles } from '../../mixins'
import SortFiles from './SortFiles'
import UploadFiles from './UploadFiles'
import Resource from './Resource/Resource'
import File from './File/File'

export default {
    mixins: [Sortable, Toasted, Loading, UploadedFiles],

    components: {
        SortFiles,
        UploadFiles,
        Resource,
        File,
    },

    props: {
        field: Object,
    },

    computed: {
        files () {
            const files = this.field.value.concat(this.uploadedFiles)

            return this.field.multiple ? files : files.slice(-1)
        }
    }
}
</script>
