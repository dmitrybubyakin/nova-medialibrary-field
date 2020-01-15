<template>
  <button
    :class="{
      'btn btn-link py-3 px-4': true,
      'text-primary dim': !disabled,
      'text-80 opacity-50': disabled,
    }"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      required: true,
    },
  },
}
</script>
