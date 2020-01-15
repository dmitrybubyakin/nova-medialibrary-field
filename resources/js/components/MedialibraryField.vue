<template>
  <Provider :field="field" :resource-name="resourceName" :resource-id="resourceId">
    <div>
      <MediaList />
      <MediaUploading v-if="addFiles" class="mt-2" />
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
    // eslint-disable-next-line
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
