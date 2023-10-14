<template>
  <PanelItem :field="field">
    <template #value>
      <MedialibraryField
        :add-files="canAddFiles"
        :field="field"
        :resource-name="resourceName"
        :resource-id="resourceId"
      />
      <HelpText
        class="help-text mt-2"
        v-if="shouldShowHelpText"
        v-html="field.helpText"
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

    /**
     * Determine help text should be shown.
     */
     shouldShowHelpText() {
      return this.field.helpText?.length > 0
    },
  },
}
</script>
