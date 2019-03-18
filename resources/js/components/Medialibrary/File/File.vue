<template>
    <div class="card hoverable relative border border-50 overflow-hidden">
        <FilePreview class="preview-width preview-height" :file="file" :style="previewStyle"/>

        <div class="absolute pin-x pin-b bg-90-half text-sm text-white p-2" :class="{ 'text-danger': file.uploadingFailed }">
            <p class="truncate">
                {{ file.thumbnailTitle }}
            </p>
            <p v-if="file.thumbnailDescription" class="pt-1 text-xs">
                {{ file.thumbnailDescription }}
            </p>
        </div>

        <div v-if="visibleLabels.length" class="absolute pin-l pin-t p-2 z-10 flex flex-col">
            <div v-for="label in visibleLabels" class="inline-block rounded-full w-3 h-3 border-2 border-white-50%  mb-1"
                :style="{ backgroundColor: label.color }"
                :title="label.title"
            ></div>
        </div>

        <div v-if="loading" class="absolute pin bg-90-half flex items-center justify-center">
            <loader class="text-white" />
        </div>

        <FileActions v-else class="absolute pin bg-90-half"
            :file="file"
            :show-actions="showActions"
            v-on="$listeners"
        />
    </div>
</template>

<script>

import FilePreview from './FilePreview'
import FileActions from './FileActions'

export default {
    components: { FilePreview, FileActions },

    props: {
        file: Object,
        width: { type: String, required: false },
        height: { type: String, required: false },
        loading: Boolean,
        showActions: Boolean,
    },

    computed: {
        previewStyle () {
            return this.width && this.height ? {
                width: this.width,
                height: this. height,
            } : {}
        },

        visibleLabels () {
            return (this.file.labels || []).filter(label => label.visible)
        }
    }
}
</script>
