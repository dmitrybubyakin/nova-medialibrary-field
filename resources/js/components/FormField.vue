<template>
    <default-field :field="field" :errors="errors" :show-help-text="!!field.helpText" :full-width-content="true" class="medialibrary-field">
        <template slot="field">
            <Medialibrary v-if="isNotEmpty" :field="field" v-model="value"/>

            <div :class="isNotEmpty ? 'border-b border-40 my-4' : 'my-2'"></div>

            <UploadFiles :field="field" v-model="filesToUpload" :errors="errors"/>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import Medialibrary from './Medialibrary/Medialibrary'
import UploadFiles from './Medialibrary/UploadFiles'
import objectToFormData from 'object-to-formdata'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    components: {
        Medialibrary,
        UploadFiles,
    },

    data () {
        return {
            filesToUpload: [],
        }
    },

    computed: {
        isNotEmpty () {
            return this.value && this.value.length
        }
    },

    methods: {
        setInitialValue () {
            this.value = this.field.value || []
        },

        fill (formData) {
            const files = this.filesToUpload.map(({ file, cropperData, customProperties }) => {
                return { file, cropperData, customProperties }
            })

            objectToFormData({ [this.field.collectionName]: files }, { indices: true }, formData)
        },

        handleChange (value) {
            this.value = value
        }
    },
}
</script>
