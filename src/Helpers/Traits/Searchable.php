<?php
/**
 * Created by PhpStorm.
 * User: LAMLAM
 * Date: 4/7/2019
 * Time: 6:05 PM
 */

namespace FoxEngineers\AdminCP\Helpers\Traits;
trait Searchable
{
    public $searchLike = [];

    public $searchable = [
        'id',
        'updated_at',
    ];

    public function setSearch($data = [])
    {
        $this->searchable = $data;
    }

    public function setSearchLike(array $data = []): void
    {
        $this->searchLike = $data;
    }

    public function getSearch(): array
    {
        return array_merge($this->searchLike, $this->searchable);
    }

    public function isSearchLike(string $keySearch): bool
    {
        if (in_array($keySearch, $this->searchLike)) {
            return true;
        }

        return false;
    }
}
