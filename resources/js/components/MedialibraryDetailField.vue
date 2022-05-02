<template>
  <PanelItem :field="field">
    <template #value>
      <MedialibraryField
        :add-files="canAddFiles"
        :field="field"
        :resource-name="resourceName"
        :resource-id="resourceId"
      />
    </template>
  </PanelItem>
</template>

<script>
import MedialibraryField from './MedialibraryField'

export default {
  components: {
    MedialibraryField,
  },

  props: ['resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    canAddFiles() {
      return !!this.field.attachOnDetails
    },
  },
}
</script>
