<template>
  <div
    v-tooltip="tooltip"
    :class="{
      'relative group border border-50 rounded-full overflow-hidden': true,
      'shadow-media-chosen border-info-dark': chosen,
      'border-50': !chosen,
    }"
  >
    <MediaPreview :media="media" class="w-24 h-24 shadow">
      <span slot="fallback" class="text-90 text-xs truncate select-none">
        {{ media.extension.toUpperCase() }}
      </span>
    </MediaPreview>

    <div
      :class="{
        'absolute pin': true,
        'group-hover:block hidden bg-overlay': !chosen,
        'bg-info-dark-half': chosen,
      }"
    >
      <button type="button" class="w-full h-full focus:outline-none flex items-center justify-center" @click="$emit(chosen ? 'unchoose' : 'choose')">
        <icon
          v-if="chosen"
          view-box="0 0 24 24"
          width="40"
          height="40"
          type="check-circle"
          class="text-white"
        />
      </button>
    </div>
  </div>
</template>

<script>
import MediaPreview from './MediaPreview'

export default {
  components: {
    MediaPreview,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
    chosen: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    tooltip() {
      return {
        classes: 'medialibrary-tooltip bg-white p-2 rounded border border-50 shadow text-sm leading-normal',
        content: `${this.media.collectionName}: ${this.media.fileName}`,
        offset: 10,
        placement: 'bottom',
      }
    },
  },
}
</script>
