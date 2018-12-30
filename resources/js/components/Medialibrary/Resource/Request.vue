<script>
import { Minimum } from 'laravel-nova'

export default {
    props: {
        request: Function,
    },

    data () {
        return {
            response: null
        }
    },

    async created () {
        this.$emit('started')

        try {
            this.response = await Minimum(this.request(), 200)
        } catch (error) {
            this.$emit('failed', error)
        }

        this.$emit('finished')
    },

    render () {
        if (! this.response) {
            return
        }

        return this.$scopedSlots.default(this.response.data)
    }
}
</script>
