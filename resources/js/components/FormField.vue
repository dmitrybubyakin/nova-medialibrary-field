<template>
    <panel-item :field="field" class="medialibrary-detail-field">
        <Medialibrary slot="value" :field="field"/>
    </panel-item>
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'
    import Medialibrary from './Medialibrary/Medialibrary'

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resource', 'resourceName', 'resourceId', 'field'],

        components: { Medialibrary }
    }
</script>
