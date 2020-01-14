import Media from './Media'

export const context = Symbol()

export const Provider = {
  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      required: true,
    },
    field: {
      type: Object,
      required: true,
    },
  },

  provide() {
    return {
      [context]: this.context,
    }
  },

  data() {
    return {
      context: {
        media: [],
        loading: false,
        field: this.field,
        resourceId: this.resourceId,
        resourceName: this.resourceName,
        refresh: this.refresh,
        setMedia: this.setMedia,
      },
    }
  },

  created() {
    this.refresh()

    Nova.$on(`nova-medialibrary-field:refresh:${this.field.attribute}`, this.refresh)
  },

  beforeDestroy() {
    Nova.$off(`nova-medialibrary-field:refresh:${this.field.attribute}`, this.refresh)
  },

  methods: {
    withLoading(promise) {
      this.context.loading = true

      return promise.finally(() => {
        this.context.loading = false
      })
    },

    fetch() {
      const { attribute } = this.field
      const { resourceName, resourceId } = this
      const params = {
        fieldUuid: this.field.value,
      }

      return this.withLoading(
        Nova
          .request()
          .get(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${resourceName}/${resourceId}/media/${attribute}`, { params })
          .then(response => response.data.map(this.wrap)),
      )
    },

    wrap(media) {
      return new Media(media, this.field.attribute, {
        viaField: this.field.attribute,
        viaResource: this.resourceName,
      })
    },

    async refresh(callback = null) {
      this.context.media.forEach(media => media.setLoading(true))

      this.setMedia(await this.fetch())

      if (typeof callback === 'function') {
        callback()
      }
    },

    setMedia(media) {
      this.context.media = media
    },
  },

  render() {
    return this.$slots.default
  },
}

export const Consumer = {
  inject: {
    context,
  },

  render() {
    return this.$scopedSlots.default(this.context)
  },
}
