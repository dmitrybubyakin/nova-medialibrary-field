Nova.booting(Vue => {
  Vue.component('icon-crop', require('./components/Icons/Crop'))
  Vue.component('icon-link', require('./components/Icons/Link'))

  Vue.component('icon-cropper-rotate', require('./components/Icons/Cropper/Rotate'))
  Vue.component('icon-cropper-lock', require('./components/Icons/Cropper/Lock'))
  Vue.component('icon-cropper-unlock', require('./components/Icons/Cropper/Unlock'))
  Vue.component('icon-cropper-zoom-in', require('./components/Icons/Cropper/ZoomIn'))
  Vue.component('icon-cropper-zoom-out', require('./components/Icons/Cropper/ZoomOut'))

  Vue.component('index-nova-medialibrary-field', require('./components/MedialibraryIndexField'))
  Vue.component('detail-nova-medialibrary-field', require('./components/MedialibraryDetailField'))
  Vue.component('form-nova-medialibrary-field', require('./components/MedialibraryFormField'))

  Vue.component('detail-nova-generated-conversions-field', require('./components/GeneratedConversionsDetailField'))
})
