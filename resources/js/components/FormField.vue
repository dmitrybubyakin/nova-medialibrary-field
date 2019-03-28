<template>
    <default-field :field="field" :errors="errors" :show-help-text="false" :full-width-content="true" class="medialibrary-field">
        <template slot="field">
            <Medialibrary :field="field" v-model="value"/>

            <div :class="isNotEmpty ? 'border-b border-40 my-4' : 'my-2'"></div>

            <UploadFiles :field="field" :errors="errors" @change="handleFilesChange"/>
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
            return this.field.value && this.field.value.length
        }
    },

    methods: {
        setInitialValue () {
            this.value = this.field.value || []
        },

        fill (formData) {
            objectToFormData({ [this.field.collectionName]: this.filesToUpload }, { indices: true }, formData)
        },

        handleChange (value) {
            this.value = value
        },

        handleFilesChange (filesToUpload) {
            this.filesToUpload = filesToUpload
        }
    },
}
</script>
