<?php

namespace FoxEngineers\AdminCP\Support\FileSystem;

use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFile
{
    /** @var string */
    public string $storagePath;

    /** @var string */
    public string $disk;

    /** @var bool */
    public bool $keepFileOnLocal = true;

    /** @var array<string, mixed> */
    public array $options = ['visibility' => 'public'];

    /** @var array<string> */
    public array $supports = ['public', 's3'];

    /** @var bool */
    public bool $renameFileName = true;

    public function __construct()
    {
        $this->disk = 'public';
        // Default will store all in /storage/app/public/files.
        $this->storagePath = 'files';
    }

    /**
     * @param string $disk
     *
     * @return bool
     */
    public function isSupport(string $disk): bool
    {
        return in_array($disk, $this->supports);
    }

    /**
     * @return string
     */
    public function getDefaultDisk(): string
    {
        return 's3';
    }

    /**
     * @param string $disk
     *
     * @return self
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisk(): string
    {
        return $this->disk;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setStoragePath(string $path): self
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getCustomStoragePath(string $path): string
    {
        return $this->storagePath . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get new name of file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    private function getFileName(UploadedFile $file): string
    {
        $name = $file->getClientOriginalName();
        if (!$this->renameFileName) {
            return $name;
        }
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $time = Carbon::now()->timestamp;

        return Str::slug($filename) . '-' . $time . '.' . $extension;
    }

    /**
     * Upload a file.
     *
     * @param UploadedFile $file - temp file.
     *
     * @param string|null $customPath
     *
     * @return array<string, mixed>|false
     */
    public function singleUpload(UploadedFile $file, string $customPath = null)
    {
        $name = $this->getFileName($file);
        $storagePath = $customPath ? $this->getCustomStoragePath($customPath) : $this->getStoragePath();

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->getDisk());
        /** @var FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        switch ($this->getDisk()) {
            case 's3':
                $localPath = $file->storeAs($storagePath, $name, ['disk' => 'public']);

                if (!$localPath) {
                    return false;
                }

                $localFile = self::pathToUploadedFile($publicDisk->path($localPath));
                $path = $disk->put('/', $localFile, $this->getOptions());

                if (!$this->keepFileOnLocal) {
                    $disk->delete($localPath);
                }

                break;
            default:
                $path = $file->storeAs($storagePath, $name, ['disk' => 'public']);
        }

        if (!is_string($path)) {
            return false;
        }

        return [
            'file_name' => $file->getClientOriginalName(),
            'path'      => $path,
            'file_size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'type'      => $file->getType(),
            'mime_type' => $file->getMimeType(),
            'server_id' => 0, //Todo:check server_id
        ];
    }

    /**
     * @param UploadedFile $file
     * @param string       $key
     * @param string|null  $path
     *
     * @return array<string, mixed>
     */
    public function quickSingleUpload(UploadedFile $file, string $key, ?string $path = null)
    {
        $data = false;
        if (!empty($file)) {
            $data = $this->singleUpload($file, $path);
        }

        if ($data === false) {
            abort(400, __('validation.uploaded', ['attribute' => $key]));
        }

        return $data;
    }

    /**
     * @param UploadedFile $file
     * @param string       $key
     * @param string|null  $path
     *
     * @return array<string, mixed>
     */
    public function quickSingleUploadImage(UploadedFile $file, string $key, ?string $path = null): array
    {
        $imageData = $this->quickSingleUpload($file, $key, $path);

        $imageSize = getimagesize($file);

        $width = 0;
        $height = 0;

        if ($imageSize != false && !empty($imageSize[0]) && $imageSize[1]) {
            $width = $imageSize[0];
            $height = $imageSize[1];
        }

        return array_merge($imageData, [
            'width'  => $width,
            'height' => $height,
        ]);
    }

    /**
     * @param UploadedFile[] $files
     * @param string|null    $customPath
     *
     * @return array<int, array<string, mixed>>|false
     */
    public function upload(array $files, ?string $customPath = null)
    {
        if (!$files || empty($files)) {
            return false;
        }
        $data = [];
        foreach ($files as $file) {
            $uploadedFile = $this->singleUpload($file, $customPath);
            if ($uploadedFile === false) {
                continue;
            }
            $data[] = $uploadedFile;
        }

        return $data;
    }

    /**
     * @param string $path
     *
     * @return UploadedFile
     */
    public static function pathToUploadedFile(string $path): UploadedFile
    {
        $name = File::name($path);
        $extension = File::extension($path);
        $originalName = $name . '.' . $extension;
        $mimeType = File::mimeType($path);
        if ($mimeType === false) {
            $mimeType = null;
        }

        return new UploadedFile($path, $originalName, $mimeType, null, false);
    }

//    public function resizeImage($path)
//    {
//        /** @var FilesystemAdapter $disk */
//        $disk = Storage::disk($this->getDisk());
//        /** Resize image. */
//        $width = Image::make($path)->width();
//        if ($width > 1600) {
//            Image::make($disk->path($path))->resize(1600)->save();
//        }
//    }
}
