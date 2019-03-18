<template>
    <modal class="select-text" @modal-close="handleClose">
        <card class="w-action-fields overflow-hidden">
            <h4 class="text-90 font-normal text-2xl flex-no-shrink px-8 pt-6">{{ __('Edit') }} {{ singularName }}</h4>

            <form v-if="fields" @submit.prevent="handleSubmit" autocomplete="off">
                <validation-errors :errors="validationErrors" />

                <div v-for="field in fields" class="action">
                    <component
                        :is="'form-' + field.component"
                        :errors="validationErrors"
                        :resource-id="resourceId"
                        :resource-name="resourceName"
                        :field="field"
                    />
                </div>

                <div class="bg-30 flex px-8 py-4">
                    <button type="button" class="btn text-80 font-normal h-9 px-3 ml-auto mr-3 btn-link" @click.prevent="handleClose">
                        {{__('Cancel')}}
                    </button>

                    <progress-button type="submit" :disabled="processing" :processing="processing">
                        {{ __('Update') }} {{ singularName }}
                    </progress-button>
                </div>
            </form>
        </card>
    </modal>
</template>

<script>
import { InteractsWithResourceInformation } from 'laravel-nova'

export default {
    props: {
        resourceName: String,
        resourceId: Number,
        resource: Object,
        fields: Array,
        validationErrors: Object,
        processing: Boolean,
    },

    mixins: [InteractsWithResourceInformation],

    data () {
        return {
            lastRetrievedAt: null,
        }
    },

    computed: {
        singularName() {
            return this.resourceInformation.singularLabel
        },

        formData() {
            return _.tap(new FormData(), formData => {
                _(this.fields).each(field => {
                    field.fill(formData)
                })

                formData.append('_method', 'PUT')
                formData.append('_retrieved_at', this.lastRetrievedAt)
            })
        },
    },

    mounted () {
        this.updateLastRetrievedAtTimestamp()
    },

    methods: {
        handleSubmit () {
            this.$emit('submit', this.formData)
        },

        handleClose () {
            this.$emit('close')
        },

        updateLastRetrievedAtTimestamp() {
            this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
        }
    }
}
</script>
