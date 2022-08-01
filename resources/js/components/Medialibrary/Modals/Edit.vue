<template>
  <Modal
    :show="show"
    @showing="handleShowingModal"
    @close-via-escape="handlePreventModalAbandonmentOnClose"
    role="dialog"
    maxWidth="2xl"
  >
    <form
      ref="theForm"
      autocomplete="off"
      @change="onUpdateFormStatus"
      @submit.prevent="handleSubmit()"
      class="overflow-hidden rounded-lg bg-white shadow-lg dark:bg-gray-800"
    >
      <div>
        <ModalHeader v-text="__('Edit Media')" />
        <!-- Validation Errors -->
        <validation-errors :errors="errors" />

        <!-- Fields -->
        <div class="action" v-for="field in fields" :key="field.attribute">
          <component
            :is="'form-' + field.component"
            :errors="errors"
            :resource-id="resourceId"
            :resource-name="resourceName"
            :field="field"
            :show-help-text="field.helpText != null"
            @field-changed="onUpdateFormStatus"
          />
        </div>

        <ModalFooter>
          <div class="ml-auto flex items-center">
            <CancelButton component="button" type="button" class="ml-auto mr-3" @click="$emit('close')" />

            <LoadingButton type="submit" ref="confirmButton" :disabled="updating" :loading="updating">
              {{ __('Update Media') }}
            </LoadingButton>
          </div>
        </ModalFooter>
      </div>
    </form>
  </Modal>
</template>

<script>
import { PreventsModalAbandonment } from 'laravel-nova'

export default {
  emits: ['confirm', 'close', 'submit'],

  mixins: [PreventsModalAbandonment],

  props: {
    show: {
      type: Boolean,
      default: false,
    },
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

  created() {
    document.addEventListener('keydown', this.handleKeydown)
  },

  beforeUnmount() {
    document.removeEventListener('keydown', this.handleKeydown)
  },

  methods: {
    handleSubmit() {
      const formData = new FormData()

      this.fields.forEach((field) => field.fill(formData))

      this.$emit('submit', formData)
    },

    /**
     * Prevent accidental abandonment only if form was changed.
     */
    onUpdateFormStatus() {
      this.updateModalStatus()
    },

    /**
     * Handle focus when modal being shown.
     */
    handleShowingModal(e) {
      // If the modal has inputs, let's highlight the first one, otherwise
      // let's highlight the submit button
      this.$nextTick(() => {
        if (this.$refs.theForm) {
          let formFields = this.$refs.theForm.querySelectorAll('input, textarea, select')

          formFields.length > 0 ? formFields[0].focus() : this.$refs.runButton.focus()
        } else {
          this.$refs.runButton.focus()
        }
      })
    },

    handlePreventModalAbandonmentOnClose() {
      this.handlePreventModalAbandonment(
        () => {
          this.$emit('close')
        },
        () => {
          e.stopPropagation()
        }
      )
    },
  },
}
</script>
