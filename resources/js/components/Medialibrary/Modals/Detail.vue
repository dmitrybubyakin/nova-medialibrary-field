<template>
  <Modal :show="show" @close-via-escape="$emit('close')" role="dialog" maxWidth="2xl">
    <div class="overflow-hidden rounded-lg bg-white shadow-lg dark:bg-gray-800">
      <ModalHeader v-text="__('Media Details')" />

      <ModalContent class="px-8">
        <template v-if="resource">
          <component
            :key="index"
            v-for="(field, index) in resource.fields"
            :index="index"
            :is="resolveComponentName(field)"
            :resource-name="resourceName"
            :resource-id="resourceId"
            :resource="resource"
            :field="field"
          />

          <div v-if="resource.fields.length == 0">
            {{ __('There are no fields to display.') }}
          </div>
        </template>
      </ModalContent>

      <ModalFooter>
        <div class="ml-auto flex items-center">
          <CancelButton component="button" type="button" class="ml-auto mr-3" @click="$emit('close')" />

          <OutlineButton v-if="canDelete" omponent="button" type="button" class="mr-3" @click="$emit('delete')">
            <Icon type="trash" width="24" height="24" />
          </OutlineButton>

          <DefaultButton v-if="canEdit" omponent="button" type="button" class="mr-3" @click="$emit('edit')">
            <Icon type="pencil-alt" width="24" height="24" />
          </DefaultButton>
        </div>
      </ModalFooter>
    </div>
  </Modal>
</template>

<script>
export default {
  emits: ['edit', 'delete', 'close'],

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
    resource: {
      type: Object,
      required: true,
    },
    readonly: {
      type: Boolean,
      default: false,
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
  },
}
</script>
