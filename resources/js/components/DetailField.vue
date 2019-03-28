<template>
    <panel-item :field="field" class="medialibrary-field">
        <Medialibrary slot="value" :field="field" v-model="value" :readonly="field.readonlyOnDetail"/>
    </panel-item>
</template>

<script>
import Medialibrary from './Medialibrary/Medialibrary'

export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    components: { Medialibrary },

    data () {
        return {
            value: this.field.value || [],
        }
    }
}
</script>
