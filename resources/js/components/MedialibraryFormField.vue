<template>
  <DefaultField :field="field" :errors="errors" :full-width-content="true" :show-help-text="showHelpText">
    <template #field>
      <MedialibraryField :add-files="true" :field="field" :resource-name="resourceName" :resource-id="resourceId" />
    </template>
  </DefaultField>
</template>

<script>
import MedialibraryField from './MedialibraryField'
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  components: {
    MedialibraryField,
  },

  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  methods: {
    setInitialValue() {
      this.value = this.field.value || ''
    },

    fill(formData) {
      formData.append(this.field.attribute, this.value || '')
    },

    handleChange(value) {
      this.value = value
    },
  },
}
</script>
