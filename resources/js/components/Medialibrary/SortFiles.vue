<template>
    <Draggable v-model="draggableItems" :options="draggableOptions">
        <slot/>
    </Draggable>
</template>

<script>
import Draggable from 'vuedraggable'

export default {
    props: {
        files: Array,
        disabled: Boolean,
    },

    components: { Draggable },

    computed: {
        draggableItems: {
            get () {
                return this.files.map(file => file.id)
            },

            set (ids) {
                this.$emit('sort', ids)
            }
        },

        draggableOptions () {
            return {
                filter: 'a, button',
                disabled: this.disabled || this.files.length < 2,
                forceFallback: navigator.userAgent.toLowerCase().indexOf('firefox') > -1, // firefox
            }
        }
    }
}
</script>
