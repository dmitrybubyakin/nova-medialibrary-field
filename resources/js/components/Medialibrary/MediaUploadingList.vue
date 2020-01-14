<template>
  <div class="flex flex-wrap -m-2">
    <MediaUploadingListItem v-for="mediaItem in media" :key="mediaItem.id" :media="mediaItem" class="m-2" />
  </div>
</template>

<script>
import MediaUploadingListItem from './MediaUploadingListItem'

export default {
  components: {
    MediaUploadingListItem,
  },

  props: {
    media: {
      type: Array,
      required: true,
    },
  },
}
</script>
