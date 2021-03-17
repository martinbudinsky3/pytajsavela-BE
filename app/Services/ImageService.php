<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as ImgService;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;


class ImageService
{
    /**
     * Get image by id
     */
    public function get($id) {
        $image = Image::findOrFail(45);
        
        $base64data = stream_get_contents($image->content);
        $image = base64_decode($base64data);
        
        return response($image, 200)->header('Content-Type', 'application/octet-stream');
    }

    /**
     * Upload image
     */
    public function store($image) {        
        $imageBytes = file_get_contents($image->getRealPath());
        $base64image = base64_encode($image);

        $savedImage = Image::create([
                        'content'=> $base64image
                    ]);

        return $savedImage->id;
    }
}