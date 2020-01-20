const defaultTooltipOptions = {
  classes: 'bg-white p-2 rounded border border-50 shadow text-sm leading-normal',
  offset: 10,
  placement: 'bottom',
}

export function tooltip(content, options = null) {
  if (!content) {
    return null
  }

  options = options || {}

  const tooltip = { ...defaultTooltipOptions, ...options, content }

  // see resources/js/components/Medialibrary/MediaList.vue handleDragStart and handleDragEnd
  tooltip.classes = `medialibrary-tooltip ${tooltip.classes}`

  return tooltip
}
