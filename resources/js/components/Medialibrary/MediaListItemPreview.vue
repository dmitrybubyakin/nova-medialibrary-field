<template>
  <div class="group relative">
    <MediaPreview :media="media" class="w-32 h-24 rounded-b" />
    <slot />
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
  },
}
</script>
