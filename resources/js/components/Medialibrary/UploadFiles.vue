<template>
    <div>
        <div class="flex flex-wrap -m-2">
            <div v-for="file in files" class="p-2">
                <File :file="file" :loading="file.loading" :show-actions="true" @delete="deleteFile(file)"/>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-default btn-primary" @click="chooseFiles">
                {{ __('Choose Files') }}
            </button>

            <progress-button v-if="fileToBeUploaded" class="ml-3"
                :disabled="uploading"
                :processing="uploading"
                @click.native="uploadFiles"
            >
                {{ __('Upload') }}
            </progress-button>
        </div>
    </div>
</template>

<script>
import File from './File/File'
import { Toasted } from '../../mixins'
import { storeFile } from '../../api'
import { Errors } from 'laravel-nova'

export default {
    mixins: [Toasted],

    props: {
        field: Object,
    },

    components: { File },

    data () {
        return {
            files: [],
            uploading: false,
        }
    },

    computed: {
        fileToBeUploaded () {
            return this.files.filter(file => !file.uploadingFailed)[0]
        }
    },

    methods: {
        chooseFiles () {
            const input = document.createElement('input')
            input.type = 'file'
            input.multiple = this.field.multiple
            input.accept = this.field.accept
            input.onchange = () => this.addFiles([...input.files])
            document.body.appendChild(input)
            input.click()
            document.body.removeChild(input)
        },

        deleteFile (fileToBeDeleted) {
            this.files = this.files.filter(file => file.id !== fileToBeDeleted.id)
        },

        addFiles (files) {
            files.forEach(callWithDelay(file => {
                file = this.wrapFile(file)

                this.field.multiple ? this.files.push(file) : (this.files = [file])

                this.loadFileThumbnailUrl(file)
            }))
        },

        wrapFile (file) {
            return {
                file,
                loading: false,
                uploadingFailed: false,
                id: Math.random().toString(36).substr(-8),
                size: file.size,
                filename: file.name,
                extension: file.name.split('.').pop(),
                thumbnailUrl: null,
                authorizedToDelete: true,
            }
        },

        loadFileThumbnailUrl (file) {
            if (! /^image/.test(file.file.type)) {
                return
            }

            file.loading = true

            const fileReader = new FileReader()

            fileReader.onload = () => setTimeout(() => {
                file.thumbnailUrl = fileReader.result
                file.loading = false
            }, 200)

            fileReader.readAsDataURL(file.file)
        },

        async uploadFiles () {
            if (! this.fileToBeUploaded) {
                return this.uploading = false
            }

            this.uploading = true

            await this.uploadFile(this.fileToBeUploaded)

            this.uploadFiles() // upload remaining files recursively
        },

        async uploadFile (file) {
            file.loading = true

            try {
                const { data } = await storeFile(this.fileToFormData(file))

                this.deleteFile(file)

                this.info(`File :filename was uploaded!`, { filename: data.filename })

                Nova.$emit(`medialibrary:uploaded-to-${this.field.collectionName}`, data)
            } catch ({ response }) {
                file.uploadingFailed = true

                if (response.status === 422) {
                    this.handleValidationErrors(file, { response })
                }
            }

            file.loading = false
        },

        handleValidationErrors ({ filename }, { response }) {
            const errors = new Errors(response.data.errors)

            this.error(`${filename}: ${errors.first('file')}`, {}, {
                duration: null,
                action: { text : 'OK', onClick : (e, toast) => toast.goAway(0)}
            })
        },

        fileToFormData (file) {
            const formData = new FormData()

            formData.append('file', file.file)
            formData.append('resource', this.field.resourceName)
            formData.append('collection', this.field.collectionName)
            formData.append('viaResource', this.$route.params.resourceName)
            formData.append('viaResourceId', this.$route.params.resourceId)
            formData.append('viaRelationship', this.field.relationName)

            return formData
        }
    }
}

const callWithDelay = (callback, delay = 100) => {
    let index = 0
    return (...args) => {
        index++
        setTimeout(() => callback(...args), delay * index)
    }
}
</script>
