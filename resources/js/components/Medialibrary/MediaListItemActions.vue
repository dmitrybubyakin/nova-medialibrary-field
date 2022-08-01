<template>
  <div class="flex items-center justify-between px-2 py-1">
    <div class="flex items-center">
      <button v-if="canView" type="button" class="flex hover:opacity-50 focus:outline-none" @click="media.view()">
        <Icon type="eye" width="18" height="18" />
      </button>

      <button v-if="canEdit" type="button" class="ml-2 flex hover:opacity-50 focus:outline-none" @click="media.edit()">
        <Icon type="pencil-alt" width="18" height="18" />
      </button>

      <button
        v-if="canDelete"
        type="button"
        class="ml-2 flex hover:opacity-50 focus:outline-none"
        @click="media.openDeleteModal()"
      >
        <Icon type="trash" width="18" height="18" />
      </button>
    </div>

    <Dropdown placement="bottom-start" class="btn-block place-self-end">
      <DropdownTrigger :show-arrow="false" class="h-6 w-6 hover:opacity-50">
        <Icon :solid="true" type="dots-horizontal" view-box="0 0 24 24" width="18" height="18" />
      </DropdownTrigger>

      <template #menu>
        <DropdownMenu>
          <div class="flex flex-col py-1">
            <DropdownMenuItem
              as="button"
              v-if="!hideCopyUrlAction"
              @click="doCopy($event, 'downloadUrl')"
              class="flex py-1 hover:bg-gray-100"
            >
              <Icon type="clipboard-copy" width="20" height="20" />
              <span class="ml-2">{{ __('Copy Url') }}</span>
            </DropdownMenuItem>

            <DropdownMenuItem
              as="button"
              class="flex items-center hover:bg-gray-100"
              v-for="copyAs in media.copyAs"
              :key="copyAs.as"
              @click="media.copy(copyAs.as)"
            >
              <Icon :type="copyAs.icon" width="20" height="20" />
              <span class="ml-2">{{ __(`Copy as ${copyAs.as}`) }}</span>
            </DropdownMenuItem>

            <DropdownMenuItem
              as="button"
              v-if="canCrop"
              class="flex items-center hover:bg-gray-100"
              @click="media.openCropperModal()"
            >
              <icon-crop width="20" height="20" />
              <span class="ml-2">{{ __('Crop') }}</span>
            </DropdownMenuItem>

            <DropdownMenuItem
              as="button"
              v-if="canRegenerate"
              class="flex items-center hover:bg-gray-100"
              @click="media.regenerate()"
            >
              <Icon type="refresh" />
              <span class="ml-2">{{ __('Regenerate') }}</span>
            </DropdownMenuItem>
          </div>
        </DropdownMenu>
      </template>
    </Dropdown>
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
