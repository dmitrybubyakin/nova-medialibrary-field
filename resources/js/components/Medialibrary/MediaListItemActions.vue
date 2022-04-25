<template>
  <div class="flex px-2 py-1">
    <button v-if="canView" type="button" class="flex hover:opacity-50 focus:outline-none" @click="media.view()">
      <Icon type="eye" width="17" height="14" />
    </button>

    <button v-if="canEdit" type="button" class="ml-2 flex hover:opacity-50 focus:outline-none" @click="media.edit()">
      <Icon type="pencil-alt" width="14" height="14" />
    </button>

    <button
      v-if="canDelete"
      type="button"
      class="ml-2 flex hover:opacity-50 focus:outline-none"
      @click="media.openDeleteModal()"
    >
      <Icon type="trash" width="14" height="14" />
    </button>

    <VDropdown :distance="6" class="ml-auto flex" :triggers="['click']" :popperTriggers="['click']">
      <button type="button" class="flex hover:opacity-50 focus:outline-none">
        <Icon :solid="true" type="dots-horizontal" view-box="0 0 24 24" width="14" height="14" />
      </button>

      <template #popper>
        <button
          v-if="!hideCopyUrlAction"
          type="button"
          class="flex w-full p-2 hover:bg-gray-50 focus:outline-none"
          @click="doCopy($event, 'downloadUrl')"
        >
          <span class="flex-none">
            <Icon type="clipboard-copy" width="14" height="14" />
          </span>
          <span class="ml-2 grow text-left text-sm">
            {{ __('Copy Url') }}
          </span>
        </button>

        <button
          v-for="copyAs in media.copyAs"
          :key="copyAs.as"
          type="button"
          class="flex w-full p-2 hover:bg-gray-50 focus:outline-none"
          @click="media.copy(copyAs.as)"
        >
          <span class="flex-none">
            <Icon :type="copyAs.icon" width="14" height="14" />
          </span>
          <span class="ml-2 grow text-left text-sm">
            {{ __(`Copy as ${copyAs.as}`) }}
          </span>
        </button>

        <button
          v-if="canCrop"
          type="button"
          class="flex w-full p-2 hover:bg-gray-50 focus:outline-none"
          @click="media.openCropperModal()"
        >
          <span class="flex-none">
            <icon-crop width="14" height="14" />
          </span>
          <span class="ml-2 grow text-left text-sm">
            {{ __('Crop') }}
          </span>
        </button>

        <button
          v-if="canRegenerate"
          type="button"
          class="flex w-full p-2 hover:bg-gray-50 focus:outline-none"
          @click="media.regenerate()"
        >
          <span class="flex-none">
            <Icon type="refresh" width="14" height="14" />
          </span>
          <span class="ml-2 grow text-left text-sm">
            {{ __('Regenerate') }}
          </span>
        </button>
      </template>
    </VDropdown>
  </div>
</template>

<script>
import { context } from './Context'

export default {
  props: {
    media: {
      type: Object,
      required: true,
    },
  },

  inject: {
    context,
  },

  methods: {
    async doCopy(event, as) {
      await this.media.copy(as, event.target)
    },
  },

  computed: {
    readonly() {
      return this.context.field.readonly
    },
    canEdit() {
      return this.media.authorizedToUpdate && !this.readonly
    },
    canView() {
      return this.media.authorizedToView
    },
    canDelete() {
      return this.media.authorizedToDelete && !this.readonly
    },
    canCrop() {
      return this.media.cropperEnabled && !this.readonly
    },
    canRegenerate() {
      return !this.readonly
    },
    hideCopyUrlAction() {
      return this.context.field.hideCopyUrlAction
    },
  },
}
</script>
