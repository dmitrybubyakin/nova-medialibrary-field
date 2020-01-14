<template>
  <Provider :field="field" :resource-name="resourceName" :resource-id="resourceId">
    <div>
      <MediaList />
      <MediaUploadingButton v-if="addFiles" class="mt-2" />
    </div>
  </Provider>
</template>

<script>
import { Provider } from './Medialibrary/Context'
import MediaList from './Medialibrary/MediaList'
import MediaUploadingButton from './Medialibrary/MediaUploadingButton'

export default {
  components: {
    Provider,
    MediaList,
    MediaUploadingButton,
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
