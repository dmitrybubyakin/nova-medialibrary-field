<template>
  <img
    v-if="usePreview"
    :src="media.previewUrl"
    :alt="media.fileName"
    class="block h-24 w-full object-cover"
    style="max-height: 100%"
    @error="loadingFailed = true"
  />
  <div v-else class="bg-40 flex items-center justify-center">
    <slot name="fallback">
      <span class="select-none truncate">
        {{ media.extension.toUpperCase() }}
      </span>
    </slot>
  </div>
</template>

<script>
export default {
  props: {
    media: {
      type: Object,
      required: true,
    },
    useFallback: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      loadingFailed: false,
    }
  },

  computed: {
    usePreview() {
      return this.media.previewUrl && !this.loadingFailed && !this.useFallback
    },
  },
}
</script>
