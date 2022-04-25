<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Unit;

use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use DmitryBubyakin\NovaMedialibraryField\Http\Requests\MedialibraryRequest;
use DmitryBubyakin\NovaMedialibraryField\MedialibraryFieldResolver;
use DmitryBubyakin\NovaMedialibraryField\Tests\Fixtures\Nova\ContainerField;
use DmitryBubyakin\NovaMedialibraryField\Tests\TestCase;
use Laravel\Nova\Fields\FieldCollection;
use TypeError;

class MedialibraryRequestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        MedialibraryFieldResolver::$resolvers = array_merge(MedialibraryFieldResolver::$resolvers, [
            ResolveFromContainerFields::class,
        ]);
    }

    /** @test */
    public function test_medialibrary_field(): void
    {
        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/media_testing_custom_attribute');
        $this->assertSame('media_testing_custom_attribute', $request->medialibraryField()->attribute);

        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/media_testing_single');
        $this->assertSame('media_testing_single', $request->medialibraryField()->attribute);

        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/media_testing_panel');
        $this->assertSame('media_testing_panel', $request->medialibraryField()->attribute);

        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/media_testing_container');
        $this->assertSame('media_testing_container', $request->medialibraryField()->attribute);
    }

    /** @test */
    public function test_medialibrary_field_error(): void
    {
        $this->expectExceptionMessage('Field with attribute `invalid-field` is not found.');

        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/invalid-field');

        $request->medialibraryField();
    }

    /** @test */
    public function test_resource_exists(): void
    {
        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/1/media/invalid-field');
        $this->assertTrue($request->resourceExists());

        $request = $this->createRequest('nova-vendor/dmitrybubyakin/nova-medialibrary-field/test-posts/undefined/media/invalid-field');
        $this->assertFalse($request->resourceExists());
    }

    /** @test */
    public function test_field_uuid(): void
    {
        $request = MedialibraryRequest::create('', 'POST', ['fieldUuid' => 'uuid']);

        $this->assertSame('uuid', $request->fieldUuid());
    }

    /** @test */
    public function test_field_uuid_error(): void
    {
        $this->expectException(TypeError::class);

        $request = MedialibraryRequest::create('', 'POST');

        $request->fieldUuid();
    }

    private function createRequest(...$args): MedialibraryRequest
    {
        $request = MedialibraryRequest::create(...$args);

        $request->setRouteResolver(function () use ($request) {
            return $this->app['router']->getRoutes()->match($request);
        });

        return $request;
    }
}

class ResolveFromContainerFields
{
    public function __invoke(FieldCollection $fields, string $attribute): ?Medialibrary
    {
        return $fields->map(function ($field) {
            if ($field instanceof ContainerField) {
                return $field->meta['fields'];
            }

            return $field;
        })
            ->flatten(1)
            ->whereInstanceOf(Medialibrary::class)
            ->findFieldByAttribute($attribute);
    }
}
