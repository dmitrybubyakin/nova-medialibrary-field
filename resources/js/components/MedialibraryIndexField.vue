<template>
  <div class="flex">
    <a
      v-for="media in mediaList"
      :key="media.id"
      v-tooltip="tooltip(media)"
      :href="media.downloadUrl"
      target="_blank"
      class="no-underline"
    >
      <MediaPreview :media="media" :class="previewClassList">
        <span slot="fallback" class="text-90 select-none truncate text-xs">
          {{ media.extension.toUpperCase() }}
        </span>
      </MediaPreview>
    </a>
  </div>
</template>

<script>
import { tooltip } from './Medialibrary/Utils'
import MediaPreview from './Medialibrary/MediaPreview'

export default {
  components: {
    MediaPreview,
  },

  // eslint-disable-next-line
  props: ['resourceName', 'field'],

  computed: {
    mediaList() {
      return this.field.value || []
    },
    previewClassList() {
      return this.field.indexPreviewClassList || 'rounded-full w-8 h-8 min-w-8 min-h-8 ml-2'
    },
  },

  methods: {
    tooltip(media) {
      return tooltip(media.tooltip)
    },
  },
}
</script>
