<template>
    <panel-item :field="field" class="medialibrary-field">
        <Medialibrary slot="value" :field="field" :readonly="true"/>
    </panel-item>
</template>

<script>
import Medialibrary from './Medialibrary/Medialibrary'

export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    components: { Medialibrary }
}
</script>
