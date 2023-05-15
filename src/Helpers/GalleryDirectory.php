<?php
/**
 * Created by PhpStorm.
 * User: LAMLAM
 * Date: 12/17/2018
 * Time: 9:26 PM
 */

namespace FoxEngineers\AdminCP\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class GalleryDirectory
{
    protected $path;

    public function __construct($path = 'app/public/photos/shares/Galleries')
    {
        $this->path = $path;
    }

    /**
     * @return Filesystem|FilesystemAdapter
     */
    public function getAdapter()
    {
        return Storage::disk('storage');
    }

    public function getDirectories(): array
    {
        $data = [];

        $adapter = $this->getAdapter();

        if(!$adapter->has($this->path)){
            $adapter->makeDirectory($this->path);
        }

        $files =  $adapter->directories($this->path);

        foreach ($files as $path) {
            $data[$path] = basename($path);
        }

        return $data;
    }
}
