<template>
  <PanelItem :field="field">
    <template #value>
      <div class="flex flex-wrap -m-2">
        <div
          v-for="(url, name) in conversions"
          :key="name"
          v-tooltip="tooltip(name)"
          class="relative group flex m-2 rounded-full overflow-hidden"
        >
          <img :src="url" :alt="name" class="w-16 h-16 object-cover" />
          <div class="group-hover:block hidden absolute pin bg-overlay">
            <div class="flex items-center justify-center h-full">
              <button type="button" class="flex text-white hover:text-primary focus:outline-none" @click="doCopy(url)">
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

    async doCopy(event, as) {
      await this.media.copy(as, event.target)
    },
  },
}
</script>
