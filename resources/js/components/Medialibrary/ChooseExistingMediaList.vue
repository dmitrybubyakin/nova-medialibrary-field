<template>
  <div class="flex flex-wrap -m-2">
    <ChooseExistingMediaListItem
      v-for="mediaItem in media"
      :key="mediaItem.id"
      :media="mediaItem"
      :chosen="isChosen(mediaItem)"
      class="m-2"
      @choose="$emit('choose', mediaItem)"
      @unchoose="$emit('unchoose', mediaItem)"
    />
  </div>
</template>

<script>
import ChooseExistingMediaListItem from './ChooseExistingMediaListItem'

export default {
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
      return this.chosenMedia.filter(m => m.id === mediaItem.id).length > 0
    },
  },
}
</script>
