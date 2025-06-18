<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductImageService
{

    const PUBLIC_DISK = 'product_public';
    const STORAGE_DISK = 'product_storage';

    public function upload(UploadedFile $image): string
    {
        $filename = 'prod_'.time().'.'.$image->extension();
        
        
        Storage::disk(self::PUBLIC_DISK)->putFileAs('', $image, $filename);
        

        Storage::disk(self::STORAGE_DISK)->putFileAs('', $image, $filename);
        
        return $filename;
    }

    public function delete(?string $filename): void
    {
        if (!$filename) return;
        
        Storage::disk(self::PUBLIC_DISK)->delete($filename);
        Storage::disk(self::STORAGE_DISK)->delete($filename);
    }
}