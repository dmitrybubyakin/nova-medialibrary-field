import IndexField from './components/MedialibraryIndexField'
import DetailField from './components/MedialibraryDetailField'
import FormField from './components/MedialibraryFormField'
import GeneratedConversionsDetailField from './components/GeneratedConversionsDetailField'

import IconCrop from './components/Icons/Crop'
import IconLink from './components/Icons/Link'

import IconCropperRotate from './components/Icons/Cropper/Rotate'
import IconCropperLock from './components/Icons/Cropper/Lock'
import IconCropperUnlock from './components/Icons/Cropper/Unlock'
import IconCropperZoomIn from './components/Icons/Cropper/ZoomIn'
import IconCropperZoomOut from './components/Icons/Cropper/ZoomOut'

import LoadingButton from './components/Buttons/LoadingButton';
import Loader from './components/Common/Loader';

Nova.booting((app, store) => {
  // Icons
  app.component('icon-crop', IconCrop)
  app.component('icon-link', IconLink)

  // Cropper icons
  app.component('icon-cropper-rotate', IconCropperRotate)
  app.component('icon-cropper-lock', IconCropperLock)
  app.component('icon-cropper-unlock', IconCropperUnlock)
  app.component('icon-cropper-zoom-in', IconCropperZoomIn)
  app.component('icon-cropper-zoom-out', IconCropperZoomOut)

  app.component('loader', Loader);

  // Buttons
  app.component('loading-button', LoadingButton);

  app.component('index-nova-medialibrary-field', IndexField)
  app.component('detail-nova-medialibrary-field', DetailField)
  app.component('form-nova-medialibrary-field', FormField)

  app.component('detail-nova-generated-conversions-field', GeneratedConversionsDetailField)
})