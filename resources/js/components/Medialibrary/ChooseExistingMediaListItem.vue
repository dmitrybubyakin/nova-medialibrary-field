<template>
  <div
    v-tooltip="tooltip"
    :class="{
      'border-50 group relative overflow-hidden rounded border': true,
      'shadow-media-chosen border-red-500': chosen,
      'border-gray-500': !chosen,
    }"
  >
    <!-- border-info-dark -->
    <MediaPreview :media="media" class="shadow">
      <span slot="fallback" class="text-90 select-none truncate text-xs">
        {{ media.extension.toUpperCase() }}
      </span>
    </MediaPreview>

    <div
      :class="{
        'absolute inset-0': true,
        'bg-overlay hidden group-hover:block': !chosen,
        'bg-info-dark-half': chosen,
      }"
    >
      <button
        type="button"
        class="focus:outline-none flex h-full w-full items-center justify-center"
        @click="$emit(chosen ? 'unchoose' : 'choose')"
      >
        <icon v-if="chosen" width="40" height="40" type="check-circle" class="text-white" />
      </button>
    </div>
  </div>
</template>

<script>
import { tooltip } from './Utils'
import MediaPreview from './MediaPreview'

export default {
  emits: ['choose', 'unchoose'],

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
      return tooltip(`${this.media.collectionName}: ${this.media.fileName}`)
    },
  },
}
</script>
