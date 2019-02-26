<template>
    <div>
        <div class="flex flex-wrap -m-2">
            <div v-for="file in files" class="p-2">
                <File
                    :file="file"
                    :width="field.thumbnailWidth"
                    :height="field.thumbnailHeight"
                    :loading="file.loading"
                    :show-actions="true"
                    @delete="deleteFile(file)"
                    @crop="openCropModal(file)"
                />
            </div>
        </div>

        <div class="mt-4">
            <input v-if="showFileInput" :id="'fileInput' + _uid" name="name" type="file" class="form-file-input"
                :multiple="field.multiple"
                :accept="field.accept"
                @change="handleFileChange"
            >

            <label :for="'fileInput' + _uid" class="form-file form-file-btn btn btn-default btn-primary">
                {{ __('Choose Files') }}
            </label>

            <progress-button v-if="fileToBeUploaded" class="ml-3"
                :disabled="uploading"
                :processing="uploading"
                @click.native="uploadFiles"
            >
                {{ __('Upload') }}
            </progress-button>
        </div>

        <portal to="modals">
            <transition name="fade">
                <CropModal v-if="cropModalOpen" :file="fileToBeCropped" @close="closeCropModal" @crop="handleCrop"/>
            </transition>
        </portal>
    </div>
</template>

<script>
import File from './File/File'
import CropModal from './Modal/CropModal'
import { Toasted } from '../../mixins'
import { storeFile } from '../../api'
import { Errors } from 'laravel-nova'
import objectToFormData from 'object-to-formdata'

export default {
    mixins: [Toasted],

    props: {
        field: Object,
    },

    components: {
        File,
        CropModal,
    },

    data () {
        return {
            files: [],
            uploading: false,
            showFileInput: true,
            fileToBeCropped: null,
        }
    },

    computed: {
        fileToBeUploaded () {
            return this.files.filter(file => !file.uploadingFailed)[0]
        },

        cropModalOpen () {
            return !!this.fileToBeCropped
        }
    },

    methods: {
        handleFileChange (event) {
            this.addFiles([...event.target.files])

            // reset file input
            this.showFileInput = false
            this.$nextTick(() => this.showFileInput = true)
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
                croppable: this.isCroppable(file),
                cropperData: null,
                cropperOriginalUrl: null,
                uploadingFailed: false,
                id: Math.random().toString(36).substr(-8),
                size: file.size,
                extension: file.name.split('.').pop(),
                thumbnailUrl: null,
                thumbnailTitle: file.name,
                authorizedToDelete: true,
            }
        },

        openCropModal (file) {
            if (! file.cropperOriginalUrl) {
                file.cropperOriginalUrl = file.thumbnailUrl
            }

            this.fileToBeCropped = file
        },

        closeCropModal () {
            this.fileToBeCropped = null
        },

        handleCrop ({ data, url }) {
            if (this.fileToBeCropped) {
                this.fileToBeCropped.cropperData = data
                this.fileToBeCropped.thumbnailUrl = url
            }

            this.closeCropModal()
        },

        isImage (file) {
            return /^image/.test(file.type)
        },

        isCroppable (file) {
            return this.field.croppable && this.isImage(file)
        },

        loadFileThumbnailUrl (file) {
            if (! this.isImage(file.file)) {
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

                this.info(`File :thumbnailTitle was uploaded!`, { thumbnailTitle: data.thumbnailTitle })

                Nova.$emit(`medialibrary:uploaded-to-${this.field.collectionName}`, data)
            } catch ({ response }) {
                file.uploadingFailed = true

                if (response.status === 422) {
                    this.handleValidationErrors(file, { response })
                } else {
                    Nova.$emit('error', (response.data && response.data.message) || this.__('Something went wrong'))
                }
            }

            file.loading = false
        },

        handleValidationErrors ({ thumbnailTitle }, { response }) {
            const errors = new Errors(response.data.errors)

            this.error(`${thumbnailTitle}: ${errors.first('file')}`, {}, {
                duration: null,
                action: { text : 'OK', onClick : (e, toast) => toast.goAway(0)}
            })
        },

        fileToFormData (file) {
            return objectToFormData({
                file: file.file,
                cropperData: file.cropperData,
                resource: this.field.resourceName,
                collection: this.field.collectionName,
                viaResource: this.$route.params.resourceName,
                viaResourceId: this.$route.params.resourceId,
                viaRelationship: this.field.relationName,
            })
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
