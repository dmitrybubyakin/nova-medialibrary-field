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

    <progress-button v-if="mediaToUpload.length" class="ml-3" @click.native="upload">
      {{ __('Upload') }}
    </progress-button>
  </div>
</template>

<script>
import { Errors } from 'laravel-nova'
import MediaUploadingList from './MediaUploadingList'
import { context } from './Context'

export default {
  components: {
    MediaUploadingList,
  },

  inject: {
    context,
  },

  data() {
    return {
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
  },

  methods: {
    processFiles(event) {
      [...event.target.files].forEach(file => {
        this.addFile(file)
      })

      this.showFileInput = false
      this.$nextTick(() => this.showFileInput = true)
    },

    addFile(file) {
      if (this.context.field.maxSize && file.size > this.context.field.maxSize) {
        Nova.error(this.__('File :filename must be less than :size kilobytes', {
          filename: file.name,
          size: this.context.field.maxSize / 1024,
        }))
        return
      }

      const id = Math.random().toString(36).substr(-8)

      if (this.context.field.single) {
        this.media = []
      }

      this.media.push({
        file,
        id,
        size: file.size,
        name: file.name,
        extension: file.name.split('.').pop(),
        uploading: false,
        uploadingFailed: false,
        uploadingProgress: 0,
        validationErrors: new Errors(),
        remove: () => this.removeFileById(id),
      })
    },

    removeFileById(id) {
      this.media = this.media.filter(media => media.id !== id)
    },

    upload() {
      const { attribute } = this.context.field
      const { resourceName, resourceId } = this.context

      this.mediaToUpload.forEach(media => {
        const formData = new FormData()
        formData.append('file', media.file)
        formData.append('fieldUuid', this.context.field.value)

        const options = {
          onUploadProgress: event => this.handleUploadingProgress(media, event),
        }

        media.uploading = true

        Nova
          .request()
          .post(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${resourceName}/${resourceId}/media/${attribute}`, formData, options)
          .then(() => this.handleUploaded(media))
          .catch(error => this.handleUploadingFailed(media, error))
      })
    },

    handleUploadingProgress(media, event) {
      media.uploadingProgress = Math.round(event.loaded / event.total * 100)
    },

    handleUploaded(media) {
      Nova.$emit(`nova-medialibrary-field:refresh:${this.context.field.attribute}`, () => {
        media.remove()
      })
    },

    handleUploadingFailed(media, error) {
      media.uploading = false
      media.uploadingFailed = true
      media.uploadingProgress = 0

      if (error.response && error.response.status === 422) {
        media.validationErrors = new Errors(error.response.data.errors)
      }
    },
  },
}
</script>
