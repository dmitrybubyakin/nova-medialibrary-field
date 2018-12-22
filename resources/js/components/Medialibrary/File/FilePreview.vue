<template>
    <img v-if="thumbnailUrl" :src="thumbnailUrl" class="block object-cover">
    <div v-else class="flex items-center justify-center">
        <span class="text-4xl truncate pb-8">{{ extensionText }}</span>
    </div>
</template>

<script>
export default {
    props: {
        file: Object,
    },

    computed: {
        thumbnailUrl () {
            return this.file.thumbnailUrl
        },

        extensionText () {
            return this.file.extension.toUpperCase()
        }
    }
}
</script>
