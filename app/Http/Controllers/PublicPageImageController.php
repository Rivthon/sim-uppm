<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPublicPageImageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PublicPageImageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UploadPublicPageImageRequest $request): JsonResponse
    {
        $path = $request->file('image')->store('public-pages/content', 'public');

        return response()->json([
            'url' => asset(Storage::disk('public')->url($path)),
        ]);
    }
}
