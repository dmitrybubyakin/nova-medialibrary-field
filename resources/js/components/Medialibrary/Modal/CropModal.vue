<template>
    <modal class="select-text" @modal-close="handleClose">
        <card class="w-action-fields overflow-hidden">
            <h4 class="text-90 font-normal text-2xl flex-no-shrink px-8 pt-6">{{ __('Crop Image') }}</h4>

            <div class="px-8 py-6">
                <VueCropper ref="cropper" :src="file.cropperOriginalUrl" :data="file.cropperData" :view-mode="3"/>
            </div>

            <div class="bg-30 flex px-8 py-4">
                <button type="button" class="btn text-80 font-normal h-9 px-3 ml-auto mr-3 btn-link" @click.prevent="handleClose">
                    {{__('Cancel')}}
                </button>

                <button type="button" class="btn btn-default btn-primary" @click="handleCrop">
                    {{ __('Crop') }}
                </button>
            </div>
        </card>
    </modal>
</template>

<script>
import VueCropper from 'vue-cropperjs'

export default {
    props: {
        file: Object,
    },

    components: {
        VueCropper
    },

    computed: {
        cropper () {
            return this.$refs.cropper
        }
    },

    methods: {
        handleClose () {
            this.$emit('close')
        },

        handleCrop () {
            this.$emit('crop', {
                data: this.cropper.getData(true),
                url: this.cropper.getCroppedCanvas().toDataURL(this.file.file.type)
            })
        }
    }
}
</script>
