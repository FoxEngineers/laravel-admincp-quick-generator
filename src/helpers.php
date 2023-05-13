<?php

use FoxEngineers\AdminCP\Helpers\HtmlHelper;
use Spatie\Html\Elements\A;
use Spatie\Html\Elements\Button;

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

if (!function_exists('include_route_files')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches subdirectories as well.
     *
     * @param string $folder
     */
    function include_route_files(string $folder): void
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('style')) {

    /**
     * @param string    $url
     * @param array     $attributes
     * @param bool|null $secure
     *
     * @return mixed
     */
    function style(string $url, array $attributes = [], ?bool $secure = null)
    {
        return resolve(HtmlHelper::class)->style($url, $attributes, $secure);
    }
}

if (!function_exists('script')) {

    /**
     * @param string    $url
     * @param array     $attributes
     * @param bool|null $secure
     *
     * @return mixed
     */
    function script(string $url, array $attributes = [], ?bool $secure = null)
    {
        return resolve(HtmlHelper::class)->script($url, $attributes, $secure);
    }
}

if (!function_exists('form_cancel')) {

    /**
     * @param string $href
     * @param string $title
     * @param string $classes
     *
     * @return A
     */
    function form_cancel(string $href, string $title, string $classes = 'btn btn-danger btn-sm'): A
    {
        return resolve(HtmlHelper::class)->formCancel($href, $title, $classes);
    }
}

if (!function_exists('form_submit')) {

    /**
     * @param string $title
     * @param string $classes
     *
     * @return Button
     */
    function form_submit(string $title, string $classes = 'btn btn-success btn-sm pull-right'): Button
    {
        return resolve(HtmlHelper::class)->formSubmit($title, $classes);
    }
}
