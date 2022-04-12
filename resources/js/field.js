Nova.booting((Vue, router, store) => {
  Nova.inertia('icon-crop', require('./components/Icons/Crop').default);
  Nova.inertia('icon-link', require('./components/Icons/Link').default);

  Nova.inertia('icon-cropper-rotate', require('./components/Icons/Cropper/Rotate').default);
  Nova.inertia('icon-cropper-lock', require('./components/Icons/Cropper/Lock').default);
  Nova.inertia('icon-cropper-unlock', require('./components/Icons/Cropper/Unlock').default);
  Nova.inertia('icon-cropper-zoom-in', require('./components/Icons/Cropper/ZoomIn').default);
  Nova.inertia('icon-cropper-zoom-out', require('./components/Icons/Cropper/ZoomOut').default);

  Nova.inertia('index-nova-medialibrary-field', require('./components/MedialibraryIndexField').default);
  Nova.inertia('detail-nova-medialibrary-field', require('./components/MedialibraryDetailField').default);
  Nova.inertia('form-nova-medialibrary-field', require('./components/MedialibraryFormField').default);

  Nova.inertia('detail-nova-generated-conversions-field', require('./components/GeneratedConversionsDetailField'))
});
