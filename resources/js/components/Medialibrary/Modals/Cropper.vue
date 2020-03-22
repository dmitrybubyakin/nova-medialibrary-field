<template>
  <modal class="select-text" @modal-close="handleClose">
    <card class="w-action-fields overflow-hidden">
      <h4 class="text-90 font-normal text-2xl flex-no-shrink px-8 pt-6">
        {{ __('Crop') }}
      </h4>

      <div class="px-8 py-6">
        <VueCropper ref="cropper" :src="media.cropperMediaUrl" :data="media.cropperData" v-bind="media.cropperOptions" style="max-height: 500px" />

        <div class="flex justify-end mt-6">
          <button v-if="cropBoxResizable" type="button" class="flex items-center ml-3 text-70 hover:text-primary focus:outline-none" @click="toggleAspectRatio">
            <icon v-if="locked" type="cropper-lock" width="16" height="16" view-box="-64 0 512 512" />
            <icon v-else type="cropper-unlock" width="16" height="16" view-box="-64 0 512 512" />
          </button>

          <button v-if="rotatable" type="button" class="flex items-center ml-3 text-70 hover:text-primary focus:outline-none" @click="rotate(-90)">
            <icon type="cropper-rotate" width="16" height="16" view-box="0 0 426.667 426.667" />
          </button>

          <button v-if="rotatable" type="button" class="flex items-center ml-3 text-70 hover:text-primary focus:outline-none" @click="rotate(90)">
            <icon type="cropper-rotate" width="16" height="16" view-box="0 0 426.667 426.667" style="transform: rotateY(180deg)" />
          </button>

          <button v-if="zoomable" type="button" class="flex items-center ml-3 text-70 hover:text-primary focus:outline-none" @click="zoom(0.15)">
            <icon type="cropper-zoom-in" width="16" height="16" view-box="0 0 512 512" />
          </button>

          <button v-if="zoomable" type="button" class="flex items-center ml-3 text-70 hover:text-primary focus:outline-none" @click="zoom(-0.15)">
            <icon type="cropper-zoom-out" width="16" height="16" view-box="0 0 512 512" />
          </button>
        </div>
      </div>

      <div class="bg-30 flex px-8 py-4">
        <button type="button" class="btn text-80 font-normal h-9 px-3 ml-auto mr-3 btn-link" @click="handleClose">
          {{ __('Cancel') }}
        </button>

        <progress-button type="button" :disabled="updating" :processing="updating" @click.native="handleCrop">
          {{ __('Crop') }}
        </progress-button>
      </div>
    </card>
  </modal>
</template>

<script>
import VueCropper from 'vue-cropperjs'
import 'cropperjs/dist/cropper.css'

export default {
  components: {
    VueCropper,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
    updating: {
      type: Boolean,
      required: true,
    },
  },

  data() {
    return {
      locked: !!this.media.cropperOptions.aspectRatio,
      rotatable: !(this.media.cropperOptions.rotatable === false),
      zoomable: !(this.media.cropperOptions.rotatable === false),
      cropBoxResizable: !(this.media.cropperOptions.cropBoxResizable === false),
    }
  },

  computed: {
    cropper() {
      return this.$refs.cropper && this.$refs.cropper.cropper
    },
  },

  methods: {
    handleClose() {
      this.$emit('close')
    },

    handleCrop() {
      this.cropper.disable()
      this.$emit('crop', this.cropper.getData(true))
    },

    rotate(value) {
      this.cropper.rotate(value)
    },

    zoom(value) {
      this.cropper.zoom(value)
    },

    toggleAspectRatio() {
      const aspectRatio = this.cropper.options.aspectRatio

      this.locked = !aspectRatio

      this.cropper.setAspectRatio(
        aspectRatio ? null : this.media.cropperOptions.aspectRatio || 1,
      )
    },
  },
}
</script>
