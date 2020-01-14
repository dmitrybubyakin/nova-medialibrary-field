<template>
  <panel-item :field="field">
    <div slot="value">
      <MedialibraryField :field="field" :resource-name="resourceName" :resource-id="resourceId" />
    </div>
  </panel-item>
</template>

<script>
import MedialibraryField from './MedialibraryField'

export default {
  components: {
    MedialibraryField,
  },

  // eslint-disable-next-line
  props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
