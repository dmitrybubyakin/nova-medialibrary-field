<template>
  <div class="group relative">
    <MediaPreview :media="media" :class="previewClassList" />
    <slot />
  </div>
</template>

<script>
import { context } from './Context'
import MediaPreview from './MediaPreview'

export default {
  components: {
    MediaPreview,
  },

  inject: {
    context,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
  },

  computed: {
    previewClassList() {
      return this.context.field.detailsPreviewClassList || 'w-32 h-24 rounded-b'
    },
  },
}
</script>
