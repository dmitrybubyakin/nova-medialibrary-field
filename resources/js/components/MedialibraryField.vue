<template>
  <Provider :field="field" :resource-name="resourceName" :resource-id="resourceId">
    <div>
      <MediaList dusk="nova-media-list" />
      <MediaUploading v-if="addFiles && !field.readonly" class="mt-2" dusk="nova-media-uploading" />
    </div>
  </Provider>
</template>

<script>
import { Provider } from './Medialibrary/Context'
import MediaList from './Medialibrary/MediaList'
import MediaUploading from './Medialibrary/MediaUploading'

export default {
  components: {
    Provider,
    MediaList,
    MediaUploading,
  },

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
    addFiles: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
