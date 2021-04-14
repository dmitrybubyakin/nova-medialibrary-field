<template>
  <div v-tooltip="tooltip" class="relative group flex w-16 h-16 bg-50 rounded-full overflow-hidden" :class="{ 'shadow-danger': media.uploadingFailed }">
    <loader v-if="previewLoading" class="text-60" :width="30" />
    <img v-if="preview" :src="preview" :alt="media.fileName" class="w-16 h-16 object-cover" :class="{ 'group-hover:opacity-75': !media.uploading }">
    <div v-if="!previewLoading && !preview" class="w-16 h-16 flex items-center justify-center" :class="{ 'group-hover:hidden': !media.uploading }">
      {{ media.extension }}
    </div>

    <div v-if="media.uploading" class="absolute pin w-full h-full bg-overlay">
      <svg class="progress-ring" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <circle
          :r="progressCircleRadius"
          :style="progressCircleStyle"
          cx="50"
          cy="50"
          class="progress-ring__circle"
          stroke="white"
          stroke-width="4"
          fill="transparent"
        />
      </svg>
    </div>

    <div v-else class="group-hover:block hidden absolute pin bg-overlay">
      <div class="flex items-center justify-center h-full">
        <button type="button" class="flex text-white hover:text-danger focus:outline-none" @click="media.remove()" dusk="nova-media-uploading-list-item-media-remove-button">
          <icon type="delete" view-box="0 0 20 20" width="20" height="20" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { tooltip } from './Utils'

export default {
  props: {
    media: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      preview: null,
      previewLoading: false,
    }
  },

  computed: {
    sizeInKb() {
      return (this.media.size / 1024).toFixed(2)
    },

    tooltipHtml() {
      return `${this.media.fileName}, ${this.sizeInKb} KB`
    },

    errorsHtml() {
      return this.media.validationErrors
        .get('file')
        .map(error => `<span class="text-danger">${error}</span>`)
        .join('<br>')
    },

    tooltip() {
      const invalid = this.media.validationErrors.has('file')

      return tooltip(`${this.tooltipHtml}<br>${this.errorsHtml}`, {
        classes: `bg-white p-2 rounded border border-${invalid ? 'danger' : '50'} shadow text-sm leading-normal`,
      })
    },

    progressCircleRadius() {
      return 46
    },

    progressCircleStyle() {
      const circumference = this.progressCircleRadius * 2 * Math.PI

      const offset = circumference - this.media.uploadingProgress / 100 * circumference

      return {
        strokeDasharray: `${circumference} ${circumference}`,
        strokeDashoffset: `${offset}`,
      }
    },
  },

  created() {
    this.loadPreview()
  },

  methods: {
    loadPreview() {
      if (this.media.isImage) {
        this.preview = this.media.previewUrl
      } else {
        return
      }

      if (this.preview) {
        return
      }

      this.previewLoading = true

      const fileReader = new FileReader()

      fileReader.onload = () => setTimeout(() => {
        this.preview = fileReader.result
        this.previewLoading = false
      }, 200)

      fileReader.readAsDataURL(this.media.file)
    },
  },
}
</script>

<style lang="scss" scoped>
.progress-ring {
  &__circle {
    transition: 0.25s stroke-dashoffset;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
  }
}
</style>
