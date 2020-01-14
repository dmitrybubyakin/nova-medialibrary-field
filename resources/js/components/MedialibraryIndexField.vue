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
      <MediaPreview :media="media" class="rounded-full w-8 h-8 ml-2">
        <span slot="fallback" class="text-90 text-xs truncate select-none">
          {{ media.extension.toUpperCase() }}
        </span>
      </MediaPreview>
    </a>
  </div>
</template>

<script>
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
  },

  methods: {
    tooltip(media) {
      return media.tooltip ? {
        classes: 'medialibrary-tooltip bg-white p-2 rounded border border-50 shadow text-sm leading-normal',
        content: media.tooltip,
        offset: 10,
        placement: 'bottom',
      } : null
    },
  },
}
</script>
