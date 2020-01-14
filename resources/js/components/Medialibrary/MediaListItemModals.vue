<template>
  <portal v-if="media.deleteModalOpen || media.detailModalOpen || media.updateModalOpen || media.cropperModalOpen" to="modals">
    <delete-resource-modal
      v-if="media.deleteModalOpen"
      mode="delete"
      @confirm="media.confirmDelete()"
      @close="media.closeDeleteModal()"
    />

    <EditModal
      v-else-if="media.updateModalOpen"
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
      :media="media"
      :updating="media.updating"
      @close="media.closeCropperModal()"
      @crop="media.crop($event)"
    />

    <DetailModal
      v-else-if="media.detailModalOpen"
      :resource-name="media.resourceName"
      :resource-id="media.resourceId"
      :resource="media.resource"
      @close="media.closeDetailModal()"
      @delete="media.openDeleteModal()"
      @edit="media.edit()"
    />
  </portal>
</template>

<script>
import CropperModal from './Modals/Cropper'
import DetailModal from './Modals/Detail'
import EditModal from './Modals/Edit'

export default {
  components: {
    CropperModal,
    DetailModal,
    EditModal,
  },

  props: {
    media: {
      type: Object,
      required: true,
    },
  },
}
</script>
