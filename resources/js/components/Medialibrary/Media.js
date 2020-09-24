import { Errors } from 'laravel-nova'
import copy from 'clipboard-copy'

export default class Media {
  constructor(media, attribute, requestParams) {
    for (let key in media) {
      this[key] = media[key]
    }


    this.__loading = false
    this.__updating = false
    this.__attribute = attribute
    this.__requestParams = requestParams

    this.__detailModalOpen = false
    this.__deleteModalOpen = false
    this.__updateModalOpen = false
    this.__cropperModalOpen = false

    this.__errors = null
    this.__fields = null
    this.__resource = null
  }

  get loading() {
    return this.__loading
  }

  get updating() {
    return this.__updating
  }

  get resource() {
    return this.__resource
  }

  get resourceId() {
    return this.id
  }

  get resourceName() {
    return 'dmitrybubyakin-nova-medialibrary-media'
  }

  get singularLabel() {
    return _.find(Nova.config.resources, resource => {
      return resource.uriKey == this.resourceName
    }).singularLabel
  }

  get fields() {
    return this.__fields
  }

  get errors() {
    return this.__errors
  }

  get detailModalOpen() {
    return this.__detailModalOpen
  }

  get updateModalOpen() {
    return this.__updateModalOpen
  }

  get deleteModalOpen() {
    return this.__deleteModalOpen
  }

  get cropperModalOpen() {
    return this.__cropperModalOpen
  }

  setLoading(loading) {
    this.__loading = loading
  }

  withLoading(promise) {
    this.setLoading(true)

    return promise.finally(() => this.setLoading(false))
  }

  withUpdating(promise) {
    this.__updating = true

    return promise.finally(() => this.__updating = false)
  }

  fetch(uri) {
    return this.withLoading(
      Nova
        .request()
        .get(`/nova-api/dmitrybubyakin-nova-medialibrary-media/${uri}`, { params: this.__requestParams })
        .then(response => response.data),
    )
  }

  openDeleteModal() {
    this.__deleteModalOpen = true
  }

  closeDeleteModal() {
    this.__deleteModalOpen = false
  }

  openDetailModal() {
    this.__detailModalOpen = true
  }

  closeDetailModal() {
    this.__detailModalOpen = false
  }

  openUpdateModal() {
    this.__updateModalOpen = true
  }

  closeUpdateModal() {
    this.__updateModalOpen = false
  }

  openCropperModal() {
    this.__cropperModalOpen = true
  }

  closeCropperModal() {
    this.__cropperModalOpen = false
  }

  closeAllModals() {
    this.closeDetailModal()
    this.closeUpdateModal()
    this.closeDeleteModal()
    this.closeCropperModal()
  }

  async view() {
    this.__resource = await this.fetch(this.id).then(response => response.resource)

    this.openDetailModal()
  }

  async edit() {
    this.__errors = new Errors()
    this.__fields = await this.fetch(`${this.id}/update-fields`).then(response => Object.values(response.fields))

    this.openUpdateModal()
  }

  async update(formData) {
    formData.append('_method', 'PUT')

    for (let key in this.__requestParams) {
      formData.append(key, this.__requestParams[key])
    }

    try {
      await this.withUpdating(
        Nova
          .request()
          .post(`/nova-api/${this.resourceName}/${this.resourceId}`, formData),
      )

      this.closeAllModals()
      this.refresh()

      Nova.success(Nova.app.__('Media was updated!', { resource: this.singularLabel.toLowerCase() }))
    } catch (error) {
      if (!error.response) {
        throw error
      } else if (error.response.status === 422) {
        this.__errors = new Errors(error.response.data.errors)
      }

      Nova.error(Nova.app.__('There was a problem submitting the form.'))
    }
  }

  async confirmDelete() {
    await Nova.request().delete(`/nova-api/${this.resourceName}`, { params: {
      ...this.__requestParams,
      resources: [this.id],
    } })

    this.closeAllModals()
    this.refresh()

    Nova.success(Nova.app.__('Media was deleted!', { resource: this.singularLabel.toLowerCase() }))
  }

  async regenerate() {
    await Nova.request().post(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${this.id}/regenerate`)

    Nova.success(Nova.app.__('Media was regenerated!', { resource: this.singularLabel.toLowerCase() }))

    this.refresh()
  }

  async copyUrl() {
    await copy(this.downloadUrl)

    Nova.success(Nova.app.__('Copied!'))
  }

  async copyExtraCopyCode() {
    await copy(this.extraCopyCode)

    Nova.success(Nova.app.__('Copied!'))
  }

  async crop(data) {
    data = { ...data, conversion: this.cropperConversion }

    await this.withUpdating(
      Nova
        .request()
        .post(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${this.id}/crop`, data),
    )

    this.closeAllModals()
    this.refresh()

    Nova.success(Nova.app.__('Media was cropped!', { resource: this.singularLabel.toLowerCase() }))
  }

  refresh() {
    Nova.$emit(`nova-medialibrary-field:refresh:${this.__attribute}`)
  }
}
