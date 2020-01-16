# Medialibrary Field for Laravel Nova

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dmitrybubyakin/nova-medialibrary-field.svg?style=flat-square)](https://github.com/dmitrybubyakin/nova-medialibrary-field/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/dmitrybubyakin/nova-medialibrary-field.svg?style=flat-square)](https://packagist.org/packages/dmitrybubyakin/nova-medialibrary-field)
[![StyleCI](https://github.styleci.io/repos/162804399/shield?branch=master)](https://github.styleci.io/repos/162804399)

Laravel Nova field for managing the Spatie media library.

**Going to release a new version soon! You can [check it out](https://github.com/dmitrybubyakin/nova-medialibrary-field/tree/feature/v2)**.
With this version you can:

 - attach media on create/update views
 - use existing media

With this package you can:

 - create, update, delete and sort your media files
 - update media attributes (filename, custom properties, etc)
 - display media on the index view
 - crop images

## Contents

 - [Screenshots](#screenshots)
 - [Requirements](#requirements)
 - [Installation](#installation)
 - [Usage](#usage)
    - [What about forms?](#what-about-forms)
    - [Display media on the index view](#display-media-on-the-index-view)
    - [Custom media realation](#custom-media-realation)
    - [Crop](#crop)
    - [Labels](#labels)
    - [Thumbnail](#thumbnail)
    - [Thumbnail size](#thumbnail-size)
    - [Thumbnail title](#thumbnail-title)
    - [Thumbnail description](#thumbnail-description)
    - [Single media collection](#single-media-collection)
    - [Store and Replace callbacks](#store-and-replace-callbacks)
    - [Custom download url](#custom-download-url)
    - [Validation](#validation)
    - [Sorting](#sorting)
    - [Custom media resource](#custom-media-resource)
    - [Authorization Gates 'view', 'update' and 'delete'](#authorization-gates-view-update-and-delete)
 - [Changelog](#changelog)
 - [Alternatives](#alternatives)
 - [License](#license)

## Screenshots

![index view](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/index.png)
![details view](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/details.png)
![media details modal](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/media-details.png)
![media update modal](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/media-update.png)
![media sorting](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/media-sorting.gif)
![media uploading](https://raw.githubusercontent.com/dmitrybubyakin/nova-medialibrary-field/master/docs/media-uploading.gif)

## Requirements

 - Laravel Nova >= 1.2.0

## Installation

This package can be installed via command:

```bash
composer require dmitrybubyakin/nova-medialibrary-field
```

In your `NovaServiceProvider` register default media resource (only if you don't want to use your own one):

```php
public function boot()
{
    parent::boot();

    Nova::resources([
        \DmitryBubyakin\NovaMedialibraryField\Resources\Media::class,
    ]);
}
```

## Usage

```php
class Post extends Resource
{
    // ...
    public function fields(Request $request)
    {
        return [
            // ...
            Medialibrary::make('Images'), // it uses default collection

            Medialibrary::make('Images', 'post_images'), // you can use collection which you want

            Medialibrary::make('Images', 'post_images', MyMediaResource::class), // you can use own Media resource
        ];
    }
}
```

### What about forms?

Medialibrary field is shown only on index and details views. If there is any reason to show it on forms, let me know.

### Display media on the index view

```php
Medialibrary::make('Images')
    ->mediaOnIndex(), // display first media

Medialibrary::make('Images')
    ->mediaOnIndex(3), // display first 3 media

Medialibrary::make('Images')
    ->mediaOnIndex(function (Collection $mediaItems) {
        return $mediaItems->where('extension', 'jpg'); // filter media
    })
```

### Custom media realation

```php
class Post extends Model implements HasMedia
{
    public function featuredMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'featured');
    }
}

Medialibrary::make('Featured Image', 'featured')
    ->relation('featuredMedia'),
```

### Crop

```php
Medialibrary::make('Featured Image', 'featured')->croppable(),
```

### Labels

```php
Medialibrary::make('Awesome Media', 'awesome_collection')
    ->label($title, $condition, $trueColor = 'var(--success)', $falseColor = 'var(--danger)'),
    ->label('Active', 'custom_properties->active'),
    ->label('Active', 'custom_properties->active', 'var(--success)', null), // visible only when the condition is true
    ->label('Active', 'custom_properties->active', null, 'var(--danger)'), // visible only when the condition is false
    ->label('Size > 1MB', function (Media $media) {
        return $media->size >= 1024 * 1024;
    }),
```

### Thumbnail

```php
Medialibrary::make('Featured Image', 'featured')
    ->thumbnail('thumbnailConversion'),

Medialibrary::make('Featured Image', 'featured')
    ->thumbnail(function (Media $media) {
        return 'https://dummyimage.com/300x300/ffffff/000000&text=' . strtoupper($media->extension);
    }),

// The default width of a thumbnail is 8 rem.
// The bigThumbnails method makes a singular thumbnail twice  as wide as the default.
Medialibrary::make('Featured Image', 'featured')
    ->thumbnail('thumbnailConversion')
    ->bigThumbnails(),
```

By default, thumbnails are available only for the files with one of the following mime types: `[image/jpeg, image/gif', image/png]`.

This is how you can override the default setting:

```php
Medialibrary::make('Featured Image', 'featured')
    ->imageMimes('image', 'mimes', 'that', 'you', 'need')
    ->thumbnail(...)
```

### Thumbnail size

```php
Medialibrary::make('Featured Image', 'featured')
    ->thumbnailSize('14rem', '9rem') // width, height
    ->thumbnailSize('10rem') // width = height
```

### Thumbnail title

```php
Medialibrary::make('Featured Image', 'featured')
    ->thumbnailTitle('custom_properties->title') // $media->file_name is used by default

Medialibrary::make('Featured Image', 'featured')
    ->thumbnailTitle(function (Media $media) {
        return $media->name;
    })
```

### Thumbnail description

You can add a short description below the thumbnail title.

```php
Medialibrary::make('Featured Image', 'featured')
    ->thumbnailDescription('custom_properties->description', $limit = 75) // hidden by default

Medialibrary::make('Featured Image', 'featured')
    ->thumbnailDescription(function (Media $media) {
        return 'Size: ' . $media->humanReadableSize;
    })
```

### Single media collection

```php
class Post extends Model implements HasMedia
{
    public function registerMediaCollections()
    {
        $this->addMediaCollection('featured')
            ->singleFile(); // just define it here
    }
}

Medialibrary::make('Featured Image', 'featured'), // nothing to do here
```

### Store and Replace callbacks

If you want to do someting before media is saved, you can use `storeUsing` method.

```php
Medialibrary::make('Images', 'post_images')
    ->storeUsing(function (FileAdder $fileAdder, UploadedFile $file) {
        return $fileAdder->withCustomProperties(['description' => $this->resource->title]);
    })
```

When you are using a single media collection, you can also use `replaceUsing` method which allows you to access the old media.

```php
Medialibrary::make('Featured Image', 'featured')
    ->replaceUsing(function (FileAdder $fileAdder, Media $oldFile, UploadedFile $file) {
        return $fileAdder
            ->usingFileName($oldFile->file_name)
            ->withCustomProperties($oldFile->custom_properties);
    })
```

### Custom download url

You can change URL of the download button.

```php
Medialibrary::make('Featured Image', 'featured')
    ->downloadUsing(function (Media $media) {
        return <...>;
    })
```


### Validation

Validation works only when you are storing a new file.

```php
Medialibrary::make('Featured Image', 'featured')
    ->rules('max:1024', 'image')
    ->accept('image/*') // this is an attribute for input. <input type="file" accept="image/*">
```

### Sorting

```php
Medialibrary::make('Images', 'post_images')
    ->sortable()
```

### Custom media resource

```php
class MyMedia extends Resource
{
    public static $model = 'Spatie\MediaLibrary\Models\Media';

    public static $displayInNavigation = false;

    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make('Filename', 'file_name'),

            Textarea::make('Description', 'custom_properties->description')->alwaysShow(),

            Text::make('Size')->displayUsing(function () {
                return $this->resource->humanReadableSize;
            })->exceptOnForms(),
        ];
    }
}

class Post extends Resource
{
    public function fields(Request $request)
    {
        return [
            Medialibrary::make('Images', 'collection', MyMedia::class),
        ];
    }
}
```

### Authorization Gates 'view', 'update' and 'delete'

To view, update and delete uploaded media, you need to setup some gates.
You can use the store and replace callbacks to store additional information to the custom_properties.
The additional information can be used inside the gates for authorization.

```php
Gate::define('view', function ($user, $media) {
    return true; // view granted
});

Gate::define('update', function ($user, $media) {
    return true; // update granted
});

Gate::define('delete', function ($user, $media) {
    return true; // deletion granted
});
```

You can also use the policy.

```php
class MediaPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Media $media): bool
    {
        return true;
    }

    public function update(User $user, Media $media): bool
    {
        return true;
    }

    public function delete(User $user, Media $media): bool
    {
        return true;
    }
}

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Media::class => MediaPolicy::class,
    ];

    //...
}
```

## Changelog

Please see the [CHANGELOG](CHANGELOG.md) for more information about the most recent changed.

## Alternatives

 - https://github.com/jameslkingsley/nova-media-library
 - https://github.com/ebess/advanced-nova-media-library

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
