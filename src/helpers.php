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

if (!function_exists('help_text')) {
    function help_text(string $text, string $url): string
    {
        $url = url($url);
        return "<p>- {$text}: <a href='{$url}' target='_blank'>{$url}</a></p>";
    }
}