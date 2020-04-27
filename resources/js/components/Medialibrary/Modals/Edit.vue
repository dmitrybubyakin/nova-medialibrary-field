<template>
  <modal class="select-text" @modal-close="handleClose">
    <card class="w-action-fields overflow-hidden">
      <h4 class="text-90 font-normal text-2xl flex-no-shrink px-8 pt-6">
        {{ __('Edit Media') }}
      </h4>

      <form v-if="fields" autocomplete="off" @submit.prevent="handleSubmit">
        <validation-errors :errors="errors" />

        <div v-for="field in fields" :key="field.attribute" class="action">
          <component
            :is="'form-' + field.component"
            :errors="errors"
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="field"
          />
        </div>

        <div class="bg-30 flex px-8 py-4">
          <button type="button" class="btn text-80 font-normal h-9 px-3 ml-auto mr-3 btn-link" @click="handleClose">
            {{ __('Cancel') }}
          </button>

          <progress-button type="submit" :disabled="updating" :processing="updating">
            {{ __('Update Media') }}
          </progress-button>
        </div>
      </form>
    </card>
  </modal>
</template>

<script>
import { InteractsWithResourceInformation } from 'laravel-nova'

export default {
  mixins: [InteractsWithResourceInformation],

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      type: [Number, String],
      required: true,
    },
    fields: {
      type: Array,
      required: true,
    },
    errors: {
      type: Object,
      required: true,
    },
    updating: {
      type: Boolean,
      required: true,
    },
  },

  methods: {
    handleSubmit() {
      const formData = new FormData()

      this.fields.forEach(field => field.fill(formData))

      this.$emit('submit', formData)
    },

    handleClose() {
      this.$emit('close')
    },
  },
}
</script>
