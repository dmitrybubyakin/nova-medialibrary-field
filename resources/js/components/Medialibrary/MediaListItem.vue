<template>
  <div v-tooltip="tooltip" class="relative overflow-hidden rounded shadow dragging:border-none">
    <MediaListItemPreview :media="media">
      <div class="dragging:hidden group-hover:block hidden absolute inset-0 bg-overlay rounded-b">
        <div class="flex items-center justify-center h-full">
          <a :href="media.downloadUrl" target="_blank" class="text-white media-item-download">
            <Icon type="download" view-box="0 0 24 24" width="32" height="32" />
          </a>
        </div>
      </div>
    </MediaListItemPreview>

    <div v-if="media.title" class="w-32 px-2 mt-1 dragging:hidden">
      <p class="text-sm truncate text-80">
        {{ media.title }}
      </p>
    </div>

    <div v-if="media.loading" class="absolute flex items-center justify-center inset-0 bg-overlay">
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
