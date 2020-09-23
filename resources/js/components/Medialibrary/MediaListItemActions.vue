<template>
  <div class="my-2 px-2 flex">
    <button v-if="canView" type="button" class="flex text-70 hover:text-primary focus:outline-none" @click="media.view()">
      <icon type="view" view-box="0 0 22 14" width="17" height="14" />
    </button>

    <button v-if="canEdit" type="button" class="flex text-70 hover:text-primary focus:outline-none ml-2" @click="media.edit()">
      <icon type="edit" view-box="0 0 20 20" width="14" height="14" />
    </button>

    <button v-if="canDelete" type="button" class="flex text-70 hover:text-primary focus:outline-none ml-2" @click="media.openDeleteModal()">
      <icon type="delete" view-box="0 0 20 20" width="14" height="14" />
    </button>

    <button v-if="media.extraCopyCode" type="button" class="flex text-70 hover:text-primary focus:outline-none ml-2" @click="media.copyExtraCopyCode()">
      <icon type="link" view-box="0 0 20 20" width="14" height="14" />
    </button>

    <v-popover class="flex ml-auto" popover-base-class="" popover-class="bg-white rounded shadow border border-30">
      <button type="button" class="flex text-70 hover:text-primary focus:outline-none">
        <icon type="more" view-box="0 0 24 24" width="14" height="14" />
      </button>
      <div slot="popover">
        <button v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.copyUrl()">
          <span class="text-80">
            <icon type="link" view-box="0 0 20 20" width="14" height="14" />
          </span>
          <span class="text-left text-sm text-90 ml-2">
            {{ __('Copy Url') }}
          </span>
        </button>

        <button v-if="canCrop" v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.openCropperModal()">
          <span class="text-80">
            <icon type="crop" view-box="0 0 561 561" width="14" height="14" />
          </span>
          <span class="text-left text-sm text-90 ml-2">
            {{ __('Crop') }}
          </span>
        </button>

        <button v-if="canRegenerate" v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.regenerate()">
          <span class="text-80">
            <icon type="refresh" view-box="0 0 24 24" width="14" height="14" />
          </span>
          <span class="text-left text-sm text-90 ml-2">
            {{ __('Regenerate') }}
          </span>
        </button>
      </div>
    </v-popover>
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
  },
}
</script>
