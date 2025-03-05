<?php

namespace Modules\Common\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class FileHelper
{
    protected UploadedFile $file;
    protected ?string $fileName = null;
    protected ?string $path = null;
    protected ?string $disk = 'public';
    protected ?string $visibility = 'public';
    protected ?int $height = 300;
    protected ?int $quality = 75;

    /**
     * Set the file to process.
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Set the file to process.
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the disk for file storage.
     *
     * @param string $disk
     * @return $this
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Set the visibility for file storage.
     *
     * @param string $visibility
     * @return $this
     */
    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Set the image height for resizing.
     *
     * @param int $height
     * @return $this
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set the image quality for compression.
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality(int $quality): self
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Generate a unique file name and set the path.
     *
     * @return $this
     */
    public function generateUniqueFileName(): self
    {
        $this->fileName = sprintf(
            '%s_%s_%s.%s',
            now()->format('YmdHis'),
            Str::random(8),
            substr(md5($this->file->hashName()), 0, 8),
            $this->file->extension()
        );


        $this->path = "{$this->path}/{$this->fileName}";

        return $this;
    }

    /**
     * Upload the image with the generated filename and path.
     *
     * @return $this
     */
    public function upload(): self
    {
        try {
            // Resize and store the image
            $image = $this->resizeImage();
            $this->storeResized($image);
        } catch (\Exception $e) {
            Log::error("Image processing failed for {$this->path}: " . $e->getMessage());
            // Store the original file if resizing fails
            $this->storeOriginal();
        }

        return $this;
    }

    /**
     * Resize the image while maintaining the aspect ratio.
     *
     * @return \Intervention\Image\Image
     */
    private function resizeImage()
    {
        $image = Image::read($this->file->path());
        $width = (int) (($image->width() / $image->height()) * $this->height);

        // Resize and maintain aspect ratio
        return $image->resize($width, $this->height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encodeByExtension($this->file->extension(), quality: $this->quality);
    }

    /**
     * Store the resized image.
     *
     * @param \Intervention\Image\Image $image
     * @return bool
     */
    private function storeResized($image): bool
    {
        $this->ensureDirectoryExists();

        // Save the image as a stream (or as binary data) before storing it
        return Storage::disk($this->disk)->put($this->path, $image, ['visibility' => $this->visibility]);
    }

    /**
     * Store the original uploaded file.
     *
     * @return bool
     */
    private function storeOriginal(): bool
    {
        $this->ensureDirectoryExists();
        return Storage::disk($this->disk)->putFileAs(dirname($this->path), $this->file, basename($this->path), ['visibility' => $this->visibility]);
    }

    /**
     * Ensure the directory exists on the specified disk.
     *
     * @return void
     */
    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->path);
        if (!Storage::disk($this->disk)->exists($directory)) {
            Storage::disk($this->disk)->makeDirectory($directory);
        }
    }

    /**
     * Get the generated file name.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Get the path of the uploaded file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
