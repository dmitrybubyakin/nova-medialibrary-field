<template>
  <DeleteResourceModal
    v-if="media.deleteModalOpen"
    :show="media.deleteModalOpen"
    mode="delete"
    @confirm="media.confirmDelete()"
    @close="media.closeDeleteModal()"
  />

  <EditModal
    v-else-if="media.updateModalOpen"
    :show="media.updateModalOpen"
    :resource-name="media.resourceName"
    :resource-id="media.resourceId"
    :fields="media.fields"
    :errors="media.errors"
    :updating="media.updating"
    @close="media.closeUpdateModal()"
    @submit="media.update($event)"
  />

  <CropperModal
    v-else-if="media.cropperModalOpen"
    :show="media.cropperModalOpen"
    :media="media"
    :updating="media.updating"
    @close="media.closeCropperModal()"
    @crop="media.crop($event)"
  />

  <DetailModal
    v-else-if="media.detailModalOpen"
    :show="media.detailModalOpen"
    :resource-name="media.resourceName"
    :resource-id="media.resourceId"
    :resource="media.resource"
    :readonly="context.field.readonly"
    @close="media.closeDetailModal()"
    @delete="media.openDeleteModal()"
    @edit="media.edit()"
  />
</template>

<script>
import { context } from './Context'
import CropperModal from './Modals/Cropper'
import DetailModal from './Modals/Detail'
import EditModal from './Modals/Edit'

export default {
  components: {
    CropperModal,
    DetailModal,
    EditModal,
  },

  inject: {
    context,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
  },
}
</script>
