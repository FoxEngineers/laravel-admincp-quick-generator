<?php
/**
 * Created by PhpStorm.
 * User: LAMLAM
 * Date: 12/17/2018
 * Time: 9:26 PM
 */

namespace FoxEngineers\AdminCP\Helpers;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;

class GalleryDirectory
{
    protected string $path;

    public function __construct($path = 'app/public/photos/shares/Galleries')
    {
        $this->path = $path;
    }

    /**
     * Get all directories of specific path.
     *
     * @return array
     * @throws FilesystemException
     */
    public function getDirectories(): array
    {
        $data = [];

        $storage = Storage::disk('storage');

        if(!$storage->has($this->path)){
            $storage->makeDirectory($this->path);
        }

        $files =  $storage->directories($this->path);

        foreach ($files as $path) {
            $data[$path] = basename($path);
        }

        return $data;
    }
}
