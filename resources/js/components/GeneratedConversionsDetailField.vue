<template>
  <panel-item :field="field">
    <div slot="value" class="flex flex-wrap -m-2">
      <div v-for="(url, name) in conversions" :key="name" v-tooltip="tooltip(name)" class="relative group flex m-2 rounded-full overflow-hidden">
        <img :src="url" :alt="name" class="w-16 h-16 object-cover">
        <div class="group-hover:block hidden absolute pin bg-overlay">
          <div class="flex items-center justify-center h-full">
            <button type="button" class="flex text-white hover:text-primary focus:outline-none" @click="copy(url)">
              <icon type="link" view-box="0 0 20 20" width="20" height="20" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </panel-item>
</template>

<script>
import copy from 'clipboard-copy'

export default {
  // eslint-disable-next-line
  props: ['resource', 'resourceName', 'resourceId', 'field'],

  computed: {
    conversions() {
      return this.field.value || {}
    },
  },

  methods: {
    tooltip(name) {
      return this.field.withTooltips ? {
        classes: 'medialibrary-tooltip bg-white p-2 rounded border border-50 shadow text-sm leading-normal',
        content: name,
        offset: 10,
        placement: 'bottom',
      } : null
    },

    async copy(url) {
      await copy(url)

      Nova.success(Nova.app.__('Copied!'))
    },
  },
}
</script>
