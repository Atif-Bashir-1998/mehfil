<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageHelper
{
    /**
     * Convert the uploaded image to WebP format and store it.
     *
     * @param UploadedFile $file The uploaded image file.
     * @param string $path The storage path.
     * @return string The path to the stored WebP image.
     */
    public static function convert_to_webp(UploadedFile $file, $path = 'uploads'): string
    {
        // Create a new image manager with the GD driver
        $manager = new ImageManager(new Driver());

        // Read the uploaded image
        $image = $manager->read($file->getRealPath());

        // Define the WebP file name
        $webp_filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';

        // Encode the image as WebP with a certain quality (65 is a good balance)
        $encoded_image = $image->encode(new WebpEncoder(quality: 65));

        // Store the WebP image in the public disk
        $webp_path = $path . '/' . $webp_filename;
        Storage::disk('public')->put($webp_path, $encoded_image);

        // Return the path to the stored WebP image
        return $webp_path;
    }
}
