<?php

use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaAttachController;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaAttachmentListController;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaCropController;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaListController;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaRegenerateController;
use DmitryBubyakin\NovaMedialibraryField\Http\Controllers\MediaSortController;
use Illuminate\Support\Facades\Route;

Route::post('sort', MediaSortController::class);
Route::post('{media}/crop', MediaCropController::class);
Route::post('{media}/regenerate', MediaRegenerateController::class);
Route::get('{resource}/{resourceId}/media/{field}', MediaListController::class);
Route::get('{resource}/{resourceId}/media/{field}/attachable', MediaAttachmentListController::class);
Route::post('{resource}/{resourceId}/media/{field}', MediaAttachController::class);