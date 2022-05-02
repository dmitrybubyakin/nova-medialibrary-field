<template>
  <Modal :show="show" @close-via-escape="$emit('close')" role="dialog">
    <div class="overflow-hidden rounded-lg bg-white shadow-lg dark:bg-gray-800">
      <ModalHeader v-text="__('Crop Media')" />

      <div>
        <VueCropper
          ref="cropper"
          :aspect-ratio="1 / 1"
          :src="media.cropperMediaUrl"
          v-bind="media.cropperOptions"
          style="max-width: 36rem"
          @ready="setInitCropperData"
        />

        <div class="flex justify-end"></div>
      </div>

      <ModalFooter>
        <div class="flex">
          <button
            v-if="resizable"
            type="button"
            class="focus:outline-none flex h-8 w-8 items-center justify-center rounded hover:opacity-50 focus:ring"
            @click="toggleAspectRatio"
          >
            <icon-cropper-lock v-if="locked" width="24" height="24" />
            <icon-cropper-unlock v-else width="24" height="24" />
          </button>

          <button
            v-if="rotatable"
            type="button"
            class="focus:outline-none ml-2 flex h-8 w-8 items-center justify-center rounded hover:opacity-50 focus:ring"
            @click="rotate(-90)"
          >
            <icon-cropper-rotate width="24" height="24" />
          </button>

          <button
            v-if="rotatable"
            type="button"
            class="focus:outline-none ml-2 flex h-8 w-8 items-center justify-center rounded hover:opacity-50 focus:ring"
            @click="rotate(90)"
          >
            <icon-cropper-rotate width="24" height="24" style="transform: rotateY(180deg)" />
          </button>

          <button
            v-if="zoomable"
            type="button"
            class="focus:outline-none ml-2 flex h-8 w-8 items-center justify-center rounded hover:opacity-50 focus:ring"
            @click="zoom(0.2)"
          >
            <icon-cropper-zoom-in width="24" height="24" />
          </button>

          <button
            v-if="zoomable"
            type="button"
            class="focus:outline-none ml-2 flex h-8 w-8 items-center justify-center rounded hover:opacity-50 focus:ring"
            @click="zoom(-0.2)"
          >
            <icon-cropper-zoom-out width="24" height="24" />
          </button>
        </div>

        <div class="ml-auto flex items-center">
          <CancelButton component="button" type="button" class="ml-auto mr-3" @click="$emit('close')" />

          <LoadingButton
            type="button"
            ref="runButton"
            :disabled="updating"
            :loading="updating"
            component="DefaultButton"
            @click.native="handleCrop()"
          >
            {{ __('Crop') }}
          </LoadingButton>
        </div>
      </ModalFooter>
    </div>
  </Modal>
</template>

<script>
import VueCropper from 'vue-cropperjs'
import { PreventsModalAbandonment } from 'laravel-nova'

import 'cropperjs/dist/cropper.css'

export default {
  components: {
    VueCropper,
  },

  emits: ['confirm', 'close', 'crop'],

  mixins: [PreventsModalAbandonment],

  props: {
    show: {
      type: Boolean,
      default: false,
    },
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
    }
  },

  computed: {
    cropper() {
      return this.$refs.cropper && this.$refs.cropper.cropper
    },
    rotatable() {
      return this.media.cropperOptions.rotatable !== false
    },
    zoomable() {
      return this.media.cropperOptions.zoomable !== false
    },
    resizable() {
      return this.media.cropperOptions.cropBoxResizable !== false
    },
  },

  methods: {
    handleCrop() {
      this.cropper.disable()
      this.$emit('crop', this.cropper.getData(true))
    },

    setInitCropperData() {
      this.cropper.setData(this.media.cropperData)
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

      this.cropper.setAspectRatio(aspectRatio ? null : this.media.cropperOptions.aspectRatio || 1)
    },
  },
}
</script>
