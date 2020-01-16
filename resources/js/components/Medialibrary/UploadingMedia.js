import { Errors } from 'laravel-nova'

export class UploadingMedia {
  constructor(props) {
    for (let key of ['id', 'size', 'fileName', 'mimeType', 'extension']) {
      if (props[key] === undefined) {
        throw new Error(`[nova-medialibrary-field]: property ${key} is required`)
      }

      this[key] = props[key]
    }

    this.uploading = false
    this.uploadingFailed = false
    this.uploadingProgress = 0
    this.validationErrors = new Errors()
  }

  get exists() {
    return false
  }

  get isImage() {
    return /^image/.test(this.mimeType)
  }

  onRemove(callback) {
    this.removeHandler = callback
  }

  remove() {
    if (typeof this.removeHandler !== 'function') {
      throw new Error('[nova-medialibrary-field]: onRemove is not called')
    }

    this.removeHandler(this)
  }

  fillFormData() {
    throw new Error('Not implemented')
  }

  handleUploadProgress({ loaded, total }) {
    this.uploadingProgress = Math.round(loaded / total * 100)
  }

  handleUploadFailed(error) {
    this.uploading = false
    this.uploadingFailed = true
    this.uploadingProgress = 0

    if (error.response && error.response.status === 422) {
      this.validationErrors = new Errors(error.response.data.errors)
    }
  }

  hasValidSize(field) {
    return field.maxSize !== undefined
      ? field.maxSize >= this.size
      : true
  }
}

export class UploadingFile extends UploadingMedia
{
  constructor(props) {
    super(props)

    this.file = props.file
  }

  static create(file) {
    const id = Math.random().toString(36).substr(-8)

    return new UploadingFile({
      file,
      id,
      size: file.size,
      fileName: file.name,
      mimeType: file.type,
      extension: file.name.split('.').pop(),
    })
  }

  fillFormData(formData) {
    formData.append('file', this.file)
  }
}

export class UploadingExistingMedia extends UploadingMedia
{
  constructor(props) {
    super(props)

    this.previewUrl = props.previewUrl
  }

  static create(media) {
    return new UploadingExistingMedia(media)
  }

  get exists() {
    return true
  }

  fillFormData(formData) {
    formData.append('media', this.id)
  }
}
