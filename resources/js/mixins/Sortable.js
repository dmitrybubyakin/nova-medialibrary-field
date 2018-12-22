import { sortFiles } from '../api'

export default {
    data () {
        return {
            filesOrder: {},
        }
    },

    computed: {
        sortedFiles () {
            return _.sortBy(this.files, file => this.filesOrder[file.id])
        },

        sortingDisabled () {
            return ! this.field.mediaSortable
        }
    },

    created () {
        this.filesOrder = this.idsToFilesOrder(this.files.map(file => file.id))

        this.listenOnSort()
    },

    methods: {
        idsToFilesOrder (ids) {
            let startOrder = 1

            return ids.reduce((filesOrder, id) => {
                filesOrder[id] = startOrder++

                return filesOrder
            }, {})
        },

        listenOnSort () {
            const listener = newFilesOrder => {
                this.filesOrder = {...this.filesOrder, ...newFilesOrder}
            }

            Nova.$on('medialibrary:sort', listener)

            this.$once('hook:beforeDestroy', () => {
                Nova.$off('medialibrary:sort', listener)
            })
        },

        async handleSort (ids) {
            await sortFiles(ids)

            Nova.$emit('medialibrary:sort', this.idsToFilesOrder(ids))

            this.info('Files were sorted!')
        }
    }
}
