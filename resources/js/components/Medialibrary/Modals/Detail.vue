<template>
  <modal class="select-text" @modal-close="handleClose">
    <card class="w-action-fields overflow-hidden">
      <div class="px-8 py-6">
        <div class="flex items-center">
          <h4 class="text-90 font-normal text-2xl flex-no-shrink">
            {{ __('Media Details') }}
          </h4>

          <div class="ml-3 w-full flex items-center justify-end">
            <button v-if="canDelete" class="btn btn-default btn-icon btn-white" :title="__('Delete')" @click="handleDelete">
              <icon type="delete" class="text-80" />
            </button>

            <button v-if="canEdit" class="btn btn-default btn-icon bg-primary ml-3" :title="__('Edit')" @click="handleEdit">
              <icon type="edit" class="text-white" style="margin-top: -2px; margin-left: 3px" />
            </button>
          </div>
        </div>

        <component
          :is="resolveComponentName(field)"
          v-for="(field, index) in resource.fields"
          :key="index"
          :class="{ 'remove-bottom-border': index === resource.fields.length - 1 }"
          :resource-name="resourceName"
          :resource-id="resourceId"
          :resource="resource"
          :field="field"
        />
      </div>
    </card>
  </modal>
</template>

<script>
export default {
  props: {
    resourceName: {
      type: String,
      required: true,
    },
    resourceId: {
      type: [Number, String],
      required: true,
    },
    resource: {
      type: Object,
      required: true,
    },
    readonly: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    canDelete() {
      return this.resource.authorizedToDelete && !this.readonly
    },
    canEdit() {
      return this.resource.authorizedToUpdate && !this.readonly
    },
  },

  methods: {
    resolveComponentName(field) {
      return field.prefixComponent ? 'detail-' + field.component : field.component
    },

    handleClose() {
      this.$emit('close')
    },

    handleDelete() {
      this.$emit('delete')
    },

    handleEdit() {
      this.$emit('edit')
    },
  },
}
</script>
