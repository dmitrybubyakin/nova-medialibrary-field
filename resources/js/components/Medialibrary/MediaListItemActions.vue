<template>
  <div class="my-2 px-2 flex">
    <button v-if="media.authorizedToView" type="button" class="flex text-70 hover:text-primary focus:outline-none" @click="media.view()">
      <icon type="view" view-box="0 0 22 14" width="17" height="14" />
    </button>

    <button v-if="media.authorizedToUpdate" type="button" class="flex text-70 hover:text-primary focus:outline-none ml-2" @click="media.edit()">
      <icon type="edit" view-box="0 0 20 20" width="14" height="14" />
    </button>

    <button v-if="media.authorizedToDelete" type="button" class="flex text-70 hover:text-primary focus:outline-none ml-2" @click="media.openDeleteModal()">
      <icon type="delete" view-box="0 0 20 20" width="14" height="14" />
    </button>

    <v-popover class="flex ml-auto">
      <button type="button" class="flex text-70 hover:text-primary focus:outline-none">
        <icon type="more" view-box="0 0 24 24" width="14" height="14" />
      </button>
      <div slot="popover" class="bg-white rounded shadow border border-30">
        <button v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.copyUrl()">
          <span class="text-80">
            <icon type="link" view-box="0 0 20 20" width="14" height="14" />
          </span>
          <span class="text-left text-sm text-90 ml-2">
            {{ __('Copy Url') }}
          </span>
        </button>

        <button v-if="media.cropperEnabled" v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.openCropperModal()">
          <span class="text-80">
            <icon type="crop" view-box="0 0 561 561" width="14" height="14" />
          </span>
          <span class="text-left text-sm text-90 ml-2">
            {{ __('Crop') }}
          </span>
        </button>

        <button v-close-popover type="button" class="w-full flex px-4 py-2 hover:bg-30 focus:outline-none" @click="media.regenerate()">
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
export default {
  props: {
    media: {
      type: Object,
      required: true,
    },
  },
}
</script>
