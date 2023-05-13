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
     * @param       $url
     * @param array $attributes
     * @param null $secure
     *
     * @return mixed
     */
    function style($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->style($url, $attributes, $secure);
    }
}

if (!function_exists('script')) {

    /**
     * @param       $url
     * @param array $attributes
     * @param null $secure
     *
     * @return mixed
     */
    function script($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->script($url, $attributes, $secure);
    }
}
