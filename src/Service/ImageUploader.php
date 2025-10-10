<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageUploader
{
    private string $targetDirectory;
    private Imagine $imagine;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        
    }

    public function uploadAndResize(UploadedFile $file, string $subdir, int $width, int $height): string
    {
        $safe = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $file->getClientOriginalName());
        $filename = $safe . '-' . uniqid() . '.' . $file->guessExtension();

        $dir = $this->targetDirectory . '/images/' . $subdir;
        $file->move($dir, $filename);

        $this->imagine->open($dir . '/' . $filename)
                      ->resize(new Box($width, $height))
                      ->save();

        return $filename;
    }
}