export function getUpdateFields (resourceName, resourceId) {
    return Nova.request().get(`/nova-api/${resourceName}/${resourceId}/update-fields`)
}

export function getFile (resourceName, resourceId) {
    return Nova.request().get(`/nova-api/${resourceName}/${resourceId}`)
}

export function updateFile (resourceName, resourceId, formData) {
    return Nova.request().post(`/nova-api/${resourceName}/${resourceId}`, formData)
}

export function deleteFile (resourceName, resources) {
    resources = _.castArray(resources)

    return Nova.request().delete(`/nova-api/${resourceName}`, { params: { resources }})
}

export function sortFiles (ids) {
    return Nova.request().post(`/nova-vendor/dmitrybubyakin/nova-medialibrary-field/sort`, { ids })
}
