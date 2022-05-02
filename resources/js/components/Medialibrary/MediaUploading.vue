<template>
  <div>
    <input
      size="lg"
      align="center"
      component="button"
      v-if="showFileInput"
      :id="'input' + _uid"
      :accept="context.field.accept"
      :multiple="!context.field.single"
      type="file"
      class="form-file-input"
      @change="processFiles"
    />

    <div class="mt-2 mb-3">
      <MediaUploadingList :media="media" />
      <p v-if="validationFailed" class="text-danger mt-3 text-sm">
        {{ __('Validation failed. Hover media to see details.') }}
      </p>
    </div>

    <label
      :for="'input' + _uid"
      class="focus:outline-none relative relative inline-flex h-9 cursor-pointer items-center justify-center rounded bg-primary-500 bg-primary-500 px-3 text-sm font-bold text-white text-white shadow shadow hover:bg-primary-400 hover:bg-primary-400 focus:ring active:bg-primary-600 active:bg-primary-600 dark:text-gray-900 dark:text-gray-900"
      dusk="media-choose-action-button"
    >
      {{ chooseButtonText }}
    </label>

    <button
      v-if="attachExistingButtonVisible"
      type="button"
      class="btn btn-default btn-primary ml-3"
      @click="attachExisting"
      dusk="media-attach-existing-button"
    >
      {{ __('Use existing') }}
    </button>

    <progress-button v-if="mediaToUpload.length" class="ml-3" @click.native="upload" dusk="media-upload-action-button">
      {{ __('Upload') }}
    </progress-button>

    <ChooseExistingMediaModal
      v-if="chooseExistingMediaModalOpen"
      :show="chooseExistingMediaModalOpen"
      :field="context.field"
      :resource-id="context.resourceId"
      :resource-name="context.resourceName"
      @close="closeChooseExistingMediaModal"
      @choose="addChosenMedia"
    />
  </div>
</template>

<script>
import { UploadingFile, UploadingExistingMedia } from './UploadingMedia'
import MediaUploadingList from './MediaUploadingList'
import { context } from './Context'
import ChooseExistingMediaModal from './Modals/ChooseExistingMedia'

export default {
  components: {
    ChooseExistingMediaModal,
    MediaUploadingList,
  },

  inject: {
    context,
  },

  data() {
    return {
      chooseExistingMediaModalOpen: false,
      showFileInput: true,
      media: [],
    }
  },

  computed: {
    mediaToUpload() {
      return this.media.filter((media) => !media.uploading)
    },

    chooseButtonText() {
      if (this.context.media.length && this.context.field.single) {
        return this.__('Replace File')
      } else if (this.context.field.single) {
        return this.__('Choose File')
      } else {
        return this.__('Choose Files')
      }
    },

    attachExistingButtonVisible() {
      return this.context.field.attachExisting
    },

    synchronousUploading() {
      return this.context.field.synchronousUploading
    },

    validationFailed() {
      return this.media.some((media) => media.validationErrors.has('file'))
    },
  },

  created() {
    Nova.$on(`nova-medialibrary-field:attach:${this.context.field.attribute}`, this.addMediaItems)
  },

  beforeDestroy() {
    Nova.$off(`nova-medialibrary-field:attach:${this.context.field.attribute}`, this.addMediaItems)
  },

  methods: {
    processFiles(event) {
      ;[...event.target.files].forEach((file) => {
        this.addMedia(UploadingFile.create(file))
      })

      this.showFileInput = false
      this.$nextTick(() => (this.showFileInput = true))

      this.autoupload()
    },

    addMedia(media) {
      if (this.validationFails(media)) {
        return
      }

      media.onRemove(this.removeMedia)

      if (this.context.field.single) {
        this.media = []
      }

      this.media.push(media)
    },

    removeMedia({ id }) {
      this.media = this.media.filter((media) => media.id !== id)
    },

    validationFails(media) {
      const { field } = this.context

      if (!media.hasValidSize(field)) {
        Nova.error(
          this.__('File :filename must be less than :size kilobytes', {
            filename: media.fileName,
            size: field.maxSize / 1024,
          })
        )
        return true
      }

      return false
    },

    attachExisting() {
      this.chooseExistingMediaModalOpen = true
    },

    closeChooseExistingMediaModal() {
      this.chooseExistingMediaModalOpen = false
    },

    addChosenMedia(mediaItems) {
      this.closeChooseExistingMediaModal()

      this.addMediaItems(mediaItems)
    },

    addMediaItems(mediaItems) {
      mediaItems.forEach((media) => {
        this.addMedia(UploadingExistingMedia.create(media))
      })

      this.autoupload()
    },

    autoupload() {
      if (this.context.field.autouploading) {
        this.upload()
      }
    },

    async upload() {
      const { attribute, value } = this.context.field
      const { resourceName, resourceId } = this.context

      for (const media of this.mediaToUpload) {
        const formData = new FormData()

        media.fillFormData(formData)

        formData.append('fieldUuid', value)

        const options = {
          onUploadProgress: (event) => media.handleUploadProgress(event),
        }

        media.uploading = true

        const uploadingPromise = Nova.request()
          .post(
            `/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${resourceName}/${resourceId}/media/${attribute}`,
            formData,
            options
          )
          .then(() => this.handleUploadSucceeded(media))
          .catch((error) => this.handleUploadFailed(media, error))

        if (this.synchronousUploading) {
          await uploadingPromise
        }
      }
    },

    handleUploadSucceeded(media) {
      Nova.$emit(`nova-medialibrary-field:refresh:${this.context.field.attribute}`, () => {
        media.remove()
      })
    },

    handleUploadFailed(media, error) {
      media.handleUploadFailed(error)

      if (media.validationErrors.has('file')) {
        Nova.error(`${media.fileName}:<br>${media.validationErrors.get('file').join('<br>')}`)
      }
    },
  },
}
</script>
