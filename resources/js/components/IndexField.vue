<template>
    <p>
        <template v-if="everyFileHasThumbnail">
            <a v-for="file in files" :href="file.downloadUrl" target="_blank" :title="file.thumbnailDescription">
                <img :src="file.thumbnailUrl" :class="thumbnailSizeClasses" class="rounded-full ml-2" style="object-fit: cover">
            </a>
        </template>

        <template v-else>
            <a v-for="file in files" :href="file.downloadUrl" class="no-underline dim text-primary font-bold" target="_blank" :title="file.thumbnailDescription">
                {{ file.thumbnailTitle }}
            </a>
        </template>

        <template v-if="isEmpty">
            &mdash;
        </template>
    </p>
</template>

<script>

export default {
    props: ['resourceName', 'field'],

    computed: {
        files () {
            return this.field.value
        },

        thumbnailSizeClasses() {
          const standardThumbnailSize = 'w-8 h-8';
          const BigThumbnailSize = 'w-16 h-16';

          return !this.field.bigIndexThumbnails ? standardThumbnailSize : BigThumbnailSize;

        },

        everyFileHasThumbnail () {
            return this.files.every(file => file.thumbnailUrl)
        },

        isEmpty () {
            return ! this.files.length
        }
    }
}
</script>
