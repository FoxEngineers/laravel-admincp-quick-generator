<?php

namespace FoxEngineers\AdminCP\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;

class GalleryDirectory
{
    protected string $path;

    public function __construct(string $path = 'app/public/photos/shares/Galleries')
    {
        $this->path = $path;
    }

    public function getAdapter(): Filesystem|FilesystemAdapter
    {
        return Storage::disk('storage');
    }

    /**
     * Get directories.
     *
     * @return array<string, string>
     *
     * @throws FilesystemException
     */
    public function getDirectories(): array
    {
        $data = [];

        $adapter = $this->getAdapter();

        if (!$adapter instanceof FilesystemAdapter) {
            return $data;
        }

        if (!$adapter->has($this->path)) {
            $adapter->makeDirectory($this->path);
        }

        $files = $adapter->directories($this->path);

        foreach ($files as $path) {
            if (!\is_string($path)) {
                continue;
            }
            $data[$path] = basename($path);
        }

        return $data;
    }
}
