<template>
  <modal class="select-text" @modal-close="handleClose">
    <loading-card :loading="initialLoading">
      <card class="w-choose-existing-media overflow-hidden">
        <h4 class="text-90 font-normal text-2xl flex-no-shrink px-8 pt-6">
          {{ __('Choose existing media') }}
        </h4>

        <div class="bg-30 px-8 py-4 mt-6">
          <form @submit.prevent="applyFilter">
            <div class="relative h-9 flex-no-shrink">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" class="fill-current absolute search-icon-center ml-3 text-70">
                <path fill-rule="nonzero" d="M14.32 12.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387a8 8 0 1 1 1.414-1.414zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
              </svg>
              <input v-model="params.name" placeholder="Search" type="search" class="appearance-none form-search w-search pl-search shadow">
            </div>
          </form>
        </div>

        <div class="px-8 py-6">
          <div v-if="loading" class="mt-6">
            <loader class="text-60" />
          </div>

          <div v-else class="mb-6">
            <ChooseExistingMediaList :media="media" :chosen-media="chosenMedia" @choose="chooseMedia" @unchoose="unchooseMedia" />
          </div>

          <nav class="flex justify-between items-center">
            <PaginationButton :disabled="!prevPage || loading" @click.native="gotoPrevPage">
              {{ __('Previous') }}
            </PaginationButton>

            <PaginationButton :disabled="!nextPage || loading" @click.native="gotoNextPage">
              {{ __('Next') }}
            </PaginationButton>
          </nav>
        </div>

        <div class="bg-30 flex px-8 py-4">
          <button type="button" class="btn text-80 font-normal h-9 px-3 ml-auto mr-3 btn-link" @click="handleClose">
            {{ __('Cancel') }}
          </button>

          <button type="button" class="btn btn-default btn-primary" @click="handleChoose">
            {{ __('Choose') }}
            <span v-if="chosenMedia.length > 0">
              ({{ chosenMedia.length }})
            </span>
          </button>
        </div>
      </card>
    </loading-card>
  </modal>
</template>

<script>
import PaginationButton from '../PaginationButton'
import ChooseExistingMediaList from '../ChooseExistingMediaList'

export default {
  components: {
    PaginationButton,
    ChooseExistingMediaList,
  },

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      type: [Number, String],
      required: true,
    },
    field: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      initialLoading: false,
      loading: false,
      media: [],
      chosenMedia: [],
      params: {
        name: '',
        mimeType: this.field.accept || '',
        maxSize: this.field.maxSize || '',
      },
      perPage: 25,
      nextPage: null,
      prevPage: null,
    }
  },

  watch: {
    'params.name': {
      handler() {
        this.applyFilter()
      },
    },
  },

  created() {
    this.initialize()
  },

  methods: {
    async initialize() {
      this.initialLoading = true

      await this.processRequest(this.fetch(this.params))

      this.initialLoading = false
    },

    fetch(params) {
      const { attribute } = this.field
      const { resourceName, resourceId } = this

      return Nova
        .request()
        .get(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/${resourceName}/${resourceId}/media/${attribute}/attachable`, { params })
        .then(response => response.data)
    },

    withLoading(promise) {
      this.loading = true

      return promise.finally(() => this.loading = false)
    },

    async processRequest(request) {
      const response = await request

      this.media = response.data
      this.prevPage = response.prev_page_url ? response.current_page - 1 : null
      this.nextPage = response.next_page_url ? response.current_page + 1 : null
    },

    applyFilter: _.debounce(function() {
      this.goto(1)
    }, 300),

    goto(page) {
      if (!page) {
        return
      }

      this.processRequest(
        this.withLoading(this.fetch({ ...this.params, page })),
      )
    },

    gotoNextPage() {
      this.goto(this.nextPage)
    },

    gotoPrevPage() {
      this.goto(this.prevPage)
    },

    handleClose() {
      this.$emit('close')
    },

    chooseMedia(media) {
      if (this.field.single) {
        this.chosenMedia = []
      }

      this.chosenMedia.push(media)
    },

    unchooseMedia(media) {
      this.chosenMedia = this.chosenMedia.filter(m => m.id !== media.id)
    },

    handleChoose() {
      this.$emit('choose', this.chosenMedia)
    },
  },
}
</script>
