<template>
  <div v-if="context.loading && context.media.length === 0">
    <loader />
  </div>
  <div v-else>
    <Draggable
      v-model="mediaList"
      item-key="id"
      :disabled="sortingDisabled"
      class="-m-2 flex flex-wrap"
      drag-class="dragging"
      @start="handleDragStart"
      @end="handleDragEnd"
    >
      <template #item="{ element }">
        <MediaListItem :media="element" class="mr-2" dusk="nova-media-list-item" />
      </template>
    </Draggable>
  </div>
</template>

<script>
import Draggable from 'vuedraggable'
import { context } from './Context'
import MediaListItem from './MediaListItem'

export default {
  inject: {
    context,
  },

  components: {
    Draggable,
    MediaListItem,
  },

  computed: {
    mediaList: {
      get() {
        return this.context.media
      },
      set(media) {
        this.setNewOrder(media)
      },
    },
    sortingDisabled() {
      return this.mediaList.length <= 1 || this.context.field.readonly || this.context.field.sortable !== true
    },
  },

  methods: {
    handleDragStart() {
      document.body.classList.add('medialibrary-tooltips-hidden')
    },

    handleDragEnd() {
      document.body.classList.remove('medialibrary-tooltips-hidden')
    },

    setNewOrder(media) {
      this.context.setMedia(media)

      Nova.request()
        .post('/nova-vendor/dmitrybubyakin/nova-medialibrary-field/sort', {
          media: media.map((media) => media.id),
        })
        .then(() => Nova.success(this.__('Media sorted')))
    },
  },
}
</script>
