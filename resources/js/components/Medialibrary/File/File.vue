<template>
    <div class="card hoverable relative border border-50 overflow-hidden">
        <FilePreview class="preview-width preview-height" :file="file"/>

        <div class="absolute pin-x pin-b bg-90-half text-sm text-white p-2 truncate" :class="{ 'text-danger': file.hasError }">
            {{ file.filename }}
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
        loading: Boolean,
        showActions: Boolean,
    }
}
</script>
