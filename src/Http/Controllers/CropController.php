<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Image\Manipulations;

class CropController
{
    public function __invoke(Request $request): JsonResponse
    {
        $media = config('media-library.media_model')::findOrFail($request->route('media'));

        $media->manipulations = [
            $this->conversion($request) => [
                'manualCrop' => $this->manualCrop(
                    (int) $request->width,
                    (int) $request->height,
                    (int) $request->x,
                    (int) $request->y
                ),
                'orientation' => $this->orientation((int) $request->rotate),
            ],
        ];

        $media->save();

        return response()->json();
    }

    private function conversion(Request $request): string
    {
        return $request->conversion;
    }

    private function manualCrop(int $width, int $height, int $x, int $y): string
    {
        return "{$width},{$height},{$x},{$y}";
    }

    private function orientation(int $rotate): string
    {
        if ($rotate < 0) {
            $rotate += 360;
        }

        switch ($rotate) {
            case 90: return (string) Manipulations::ORIENTATION_90;
            case 180: return (string) Manipulations::ORIENTATION_180;
            case 270: return (string) Manipulations::ORIENTATION_270;
        }

        return Manipulations::ORIENTATION_AUTO;
    }
}
