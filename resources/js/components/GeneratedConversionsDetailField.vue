<template>
  <PanelItem :field="field">
    <template #value>
      <div class="-m-2 flex flex-wrap">
        <div
          v-for="(url, name) in conversions"
          :key="name"
          v-tooltip="tooltip(name)"
          class="group relative m-2 flex overflow-hidden rounded"
        >
          <img :src="url" :alt="name" class="h-16 w-16 object-cover" />
          <div class="pin bg-overlay absolute hidden group-hover:block">
            <div class="flex h-full items-center justify-center">
              <button type="button" class="hover:text-primary focus:outline-none flex text-white" @click="doCopy(url)">
                <icon type="link" view-box="0 0 20 20" width="20" height="20" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { Localization } from 'laravel-nova'
import Clipboard from 'clipboard'

export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    conversions() {
      return this.field.value || {}
    },
  },

  methods: {
    tooltip(name) {
      return this.field.withTooltips
        ? {
            classes: 'medialibrary-tooltip bg-white p-2 rounded border border-50 shadow text-sm leading-normal',
            content: name,
            offset: 10,
            placement: 'bottom',
          }
        : null
    },

    async doCopy(url) {
      Clipboard.copy(url, {
        container: typeof container === 'object' ? container : document.body,
      })

      Nova.success(Localization.methods.__('Copied!'))
    },
  },
}
</script>
