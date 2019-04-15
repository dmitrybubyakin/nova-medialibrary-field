<template>
    <div class="hidden card-hover:flex flex-col justify-center">
        <div v-if="showActions" class="absolute pin-t pin-x p-2 flex justify-end">
            <button v-if="file.authorizedToView" type="button" :class="actionButtonClass" @click="handleView" :title="__('View')">
                <icon type="view" view-box="0 0 22 14" width="20" height="16"/>
            </button>

            <button v-if="file.authorizedToUpdate" type="button" :class="actionButtonClass" @click="handleUpdate" :title="__('Edit')">
                <icon type="edit" view-box="0 0 20 20" width="16" height="16"/>
            </button>

            <button v-if="file.croppable" type="button" :class="actionButtonClass" @click="handleCrop" :title="__('Crop')">
                <icon type="crop" view-box="0 0 561 561" width="18" height="18"/>
            </button>

            <button v-if="file.authorizedToDelete" type="button" :class="actionButtonClass" @click="handleDelete" :title="__('Delete')">
                <icon type="delete" view-box="0 0 20 20" width="16" height="16"/>
            </button>
        </div>

        <div v-if="file.downloadUrl" class="flex justify-center">
            <a :href="file.downloadUrl" target="_blank" class="cursor-pointer text-white hover:text-primary">
                <icon type="download" view-box="0 0 24 24" width="32" height="32"/>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        file: Object,
        showActions: Boolean,
    },

    computed: {
        actionButtonClass () {
            return 'cursor-pointer text-white hover:text-primary focus:outline-none mx-1'
        }
    },

    methods: {
        handleView () {
            this.$emit('view')
        },

        handleUpdate () {
            this.$emit('update')
        },

        handleDelete () {
            this.$emit('delete')
        },

        handleCrop () {
            this.$emit('crop')
        }
    }
}
</script>
