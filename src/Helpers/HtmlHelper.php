<?php

namespace FoxEngineers\AdminCP\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Routing\UrlGenerator;
use Spatie\Html\Elements\A;
use Spatie\Html\Elements\Button;

/**
 * Class HtmlHelper.
 */
class HtmlHelper
{
    /**
     * The URL generator instance.
     *
     * @var UrlGenerator
     */
    protected UrlGenerator $url;

    /**
     * HtmlHelper constructor.
     *
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * @param string    $url
     * @param array     $attributes
     * @param bool|null $secure
     *
     * @return HtmlString
     */
    public function style(string $url, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $defaults = ['media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet'];

        $attributes = $attributes + $defaults;

        $attributes['href'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<link'.$this->attributes($attributes).'>'.PHP_EOL);
    }

    /**
     * Generate a link to a JavaScript file.
     *
     * @param string $url
     * @param array  $attributes
     * @param bool   $secure
     *
     * @return HtmlString
     */
    public function script(string $url, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $attributes['src'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<script'.$this->attributes($attributes).'></script>'.PHP_EOL);
    }

    /**
     * @param string $href
     * @param string $title
     * @param string $classes
     *
     * @return A
     */
    public function formCancel(string $href, string $title,
                               string $classes = 'btn btn-danger btn-sm'): A
    {
        return html()->a($href, $title)->class($classes);
    }

    /**
     * @param string $title
     * @param string $classes
     *
     * @return Button
     */
    public function formSubmit(string $title, string $classes = 'btn btn-success btn-sm pull-right'): Button
    {
        return html()->submit($title)->class($classes);
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function attributeElement(string $key, string $value): string
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numerics.
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key != 'value') {
            return $value ? $key : '';
        }

        if (! is_null($value)) {
            return $key.'="'.e($value).'"';
        }

        return '';
    }

    /**
     * Transform the string to a Html serializable object.
     *
     * @param $html
     *
     * @return HtmlString
     */
    protected function toHtmlString($html): HtmlString
    {
        return new HtmlString($html);
    }
}
