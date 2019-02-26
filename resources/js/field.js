Nova.booting((Vue, router) => {
    Vue.component('icon-crop', require('./components/Medialibrary/Icons/Crop'))
    Vue.component('index-nova-medialibrary-field', require('./components/IndexField'))
    Vue.component('detail-nova-medialibrary-field', require('./components/DetailField'))
    Vue.component('form-nova-medialibrary-field', require('./components/FormField'))
})
