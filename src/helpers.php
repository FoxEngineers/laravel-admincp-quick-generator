<?php

if (!function_exists('real_path')) {
    function real_path(?string $url): string
    {
        if (!is_string($url)) {
            return '';
        }

        return str_starts_with($url, '/') ? url($url) : $url;
    }
}