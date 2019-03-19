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

export default {
    mixins: [Toasted],

    props: {
        field: Object,
        errors: Object,
    },

    components: {
        File,
        CropModal,
    },

    data () {
        return {
            files: [],
            showFileInput: true,
            fileToBeCropped: null,
        }
    },

    computed: {
        cropModalOpen () {
            return !!this.fileToBeCropped
        }
    },

    watch: {
        errors: {
            deep: true,
            handler (errors) {
                this.handleValidationErrors(errors)
            }
        },

        files: {
            deep: true,
            handler (files) {
                this.$emit('change', files.map(({ file, cropperData }) => {
                    return { file, cropperData }
                }))
            }
        },
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

            fileReader.onload = () => this.$nextTick(() => {
                file.thumbnailUrl = fileReader.result
                file.loading = false
            })

            fileReader.readAsDataURL(file.file)
        },

        handleValidationErrors (errors) {
            errors = errors.all()

            const fieldErrors = Object.keys(errors).filter(key => key.startsWith(`${this.field.collectionName}.`))

            fieldErrors.forEach(key => {
               const file = this.files[+key.split('.')[1]]

                file.uploadingFailed = true

                const message = (errors[key][0] || '').replace(key, 'file')

                this.error(`${file.file.name}: ${message}`, {}, {
                    duration: null,
                    action: { text : 'OK', onClick : (e, toast) => toast.goAway(0)}
                })
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
