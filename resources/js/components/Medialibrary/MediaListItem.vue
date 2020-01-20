<template>
  <div v-tooltip="tooltip" class="dragging:border-none relative border border-40 rounded overflow-hidden shadow">
    <MediaListItemPreview :media="media">
      <div class="dragging:hidden group-hover:block hidden absolute pin bg-overlay rounded-b">
        <div class="h-full flex justify-center items-center">
          <a :href="media.downloadUrl" target="_blank" class="media-item-download text-white">
            <icon type="download" view-box="0 0 24 24" width="32" height="32" />
          </a>
        </div>
      </div>
    </MediaListItemPreview>

    <div v-if="media.title" class="dragging:hidden w-32 mt-1 px-2">
      <p class="text-80 text-sm truncate">
        {{ media.title }}
      </p>
    </div>

    <div v-if="media.loading" class="absolute pin bg-90-half flex items-center justify-center">
      <loader class="text-white" />
    </div>

    <MediaListItemActions :media="media" class="dragging:hidden" />
    <MediaListItemModals :media="media" class="dragging:hidden" />
  </div>
</template>

<script>
import { tooltip } from './Utils'
import MediaListItemActions from './MediaListItemActions'
import MediaListItemPreview from './MediaListItemPreview'
import MediaListItemModals from './MediaListItemModals'

export default {
  components: {
    MediaListItemActions,
    MediaListItemPreview,
    MediaListItemModals,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
  },

  computed: {
    tooltip() {
      return tooltip(this.media.tooltip)
    },
  },
}
</script>
