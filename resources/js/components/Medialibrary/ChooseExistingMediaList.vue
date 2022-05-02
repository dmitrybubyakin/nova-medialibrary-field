<template>
  <div class="grid grid-cols-5 gap-2">
    <ChooseExistingMediaListItem
      v-for="mediaItem in media"
      :key="mediaItem.id"
      :media="mediaItem"
      :chosen="isChosen(mediaItem)"
      @choose="$emit('choose', mediaItem)"
      @unchoose="$emit('unchoose', mediaItem)"
    />
  </div>
</template>

<script>
import ChooseExistingMediaListItem from './ChooseExistingMediaListItem'

export default {
  emits: ['choose', 'unchoose'],

  components: {
    ChooseExistingMediaListItem,
  },

  props: {
    media: {
      type: Array,
      required: true,
    },
    chosenMedia: {
      type: Array,
      required: true,
    },
  },

  methods: {
    isChosen(mediaItem) {
      return this.chosenMedia.filter((m) => m.id === mediaItem.id).length > 0
    },
  },
}
</script>
