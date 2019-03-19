export default {
    methods: {
        async refreshFields () {
            const { resourceName, resourceId } = this.$route.params

            const { data: { resource: { fields }}} = await Nova.request().get(`/nova-api/${resourceName}/${resourceId}`)

            fields.filter(field => field.component === 'nova-medialibrary-field').forEach(field => {
                Nova.$emit(`medialibrary:field-${field.collectionName}-updated`, field)
            })
        }
    }
}
