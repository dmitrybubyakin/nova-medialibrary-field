<template>
  <div>
    <input
      v-if="showFileInput"
      :id="'input' + _uid"
      :accept="context.field.accept"
      :multiple="!context.field.single"
      type="file"
      class="form-file-input"
      @change="processFiles"
    >

    <div class="mt-2 mb-3">
      <MediaUploadingList :media="media" />
    </div>

    <label :for="'input' + _uid" class="form-file form-file-btn btn btn-default btn-primary">
      {{ chooseButtonText }}
    </label>

    <button v-if="attachExistingButtonVisible" type="button" class="btn btn-default btn-primary ml-3" @click="attachExisting">
      {{ __('Use existing') }}
    </button>

    <progress-button v-if="mediaToUpload.length" class="ml-3" @click.native="upload">
      {{ __('Upload') }}
    </progress-button>

    <portal v-if="chooseExistingMediaModalOpen" to="modals">
      <ChooseExistingMediaModal
        :field="context.field"
        :resource-id="context.resourceId"
        :resource-name="context.resourceName"
        @close="closeChooseExistingMediaModal"
        @choose="addChosenMedia"
      />
    </portal>
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
      return this.media.filter(media => !media.uploading)
    },

    chooseButtonText() {
      if (this.context.media.length && this.context.field.single) {
        return this.__('Replace file')
      } else if (this.context.field.single) {
        return this.__('Choose file')
      } else {
        return this.__('Choose Files')
      }
    },

    attachExistingButtonVisible() {
      return this.context.field.attachExisting
    },
  },

  methods: {
    processFiles(event) {
      [...event.target.files].forEach(file => {
        this.addMedia(UploadingFile.create(file))
      })

      this.showFileInput = false
      this.$nextTick(() => this.showFileInput = true)

      if (this.context.field.autouploading) {
        this.upload()
      }
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
      this.media = this.media.filter(media => media.id !== id)
    },

    validationFails(media) {
      const { field } = this.context

      if (!media.hasValidSize(field)) {
        Nova.error(this.__('File :filename must be less than :size kilobytes', {
          filename: media.fileName,
          size: field.maxSize / 1024,
        }))
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

      mediaItems.forEach(media => {
        this.addMedia(UploadingExistingMedia.create(media))
      })
    },

    upload() {
      const { attribute, value } = this.context.field
      const { resourceName, resourceId } = this.context

      this.mediaToUpload.forEach(media => {
        const formData = new FormData()

        media.fillFormData(formData)

        formData.append('fieldUuid', value)

        const options = {
          onUploadProgress: event => media.handleUploadProgress(event),
        }

        media.uploading = true

        Nova
          .request()
          .post(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${resourceName}/${resourceId}/media/${attribute}`, formData, options)
          .then(() => this.handleUploadSucceeded(media))
          .catch(error => this.handleUploadFailed(media, error))
      })
    },

    handleUploadSucceeded(media) {
      Nova.$emit(`nova-medialibrary-field:refresh:${this.context.field.attribute}`, () => {
        media.remove()
      })
    },

    handleUploadFailed(media, error) {
      media.handleUploadFailed(error)
    },
  },
}
</script>
