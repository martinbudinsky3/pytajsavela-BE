<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as ImgService;
use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;


class ImageService
{
    /**
     * Get image by id
     */
    public function get($id) {
        try {
            $image = Image::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return null;
        }
        
        $base64data = stream_get_contents($image->content);
        $imageBytes = base64_decode($base64data);
        
        return $imageBytes;
    }

    /**
     * Upload image
     */
    public function store($image) {        
        $imageBytes = file_get_contents($image->getRealPath());
        $base64image = base64_encode($imageBytes);

        $savedImage = Image::create([
                        'content'=> $base64image
                    ]);

        return $savedImage->id;
    }
}