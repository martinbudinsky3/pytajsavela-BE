<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function show($id) {
        $image = $this->imageService->get($id);

        Log::debug($image);

        return response($image, 200)->header('Content-Type', 'application/octet-stream');
    }
}
