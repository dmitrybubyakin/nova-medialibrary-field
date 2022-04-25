<template>
  <div v-tooltip="tooltip" class="dragging:border-none relative overflow-hidden rounded shadow">
    <MediaListItemPreview :media="media">
      <div class="dragging:hidden bg-overlay absolute inset-0 hidden rounded-b group-hover:block">
        <div class="flex h-full items-center justify-center">
          <a :href="media.downloadUrl" target="_blank" class="media-item-download text-white">
            <Icon type="download" view-box="0 0 24 24" width="32" height="32" />
          </a>
        </div>
      </div>
    </MediaListItemPreview>

    <div v-if="media.title" class="dragging:hidden mt-1 w-32 px-2">
      <p class="text-80 truncate text-sm">
        {{ media.title }}
      </p>
    </div>

    <div v-if="media.loading" class="bg-overlay absolute inset-0 flex items-center justify-center">
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
