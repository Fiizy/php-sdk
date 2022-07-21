<?php

namespace Fiizy\Api;

use Fiizy\Api\Model\WidgetRequest;

/**
 * Widget api requests.
 */
class Widget
{
    /**
     * @var Client api client
     */
    protected $api;

    /**
     * API client.
     *
     * @param Client $api
     */
    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    /**
     * Get widget.
     *
     * @param WidgetRequest $request
     * @param boolean $cache whether result should be cached or not
     * @param string|null $cacheKey specified string will be used as cache key, if omitted then key will be generated based on request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function get(WidgetRequest $request, $cache = false, $cacheKey = null)
    {
        $variables = $request->variables();
        $widget = $this->api->fetch($this->replace($request->url, $variables), $cache, $cacheKey);
        return $this->replace($widget, $variables);
    }

    /**
     * Replace placeholders in text with variables, if placeholder variable is missing replace with empty string.
     *
     * @param string $text text with placeholder
     * @param array $variables array of variables
     *
     * @return string|null
     */
    protected function replace($text, $variables)
    {
        return preg_replace_callback(
            '/{{([^}]+)}}/',
            function ($matches) use ($variables) {
                $key = $matches[1];

                if (!isset($variables[$key])) {
                    return '';
                }

                return $variables[$key];
            },
            $text
        );
    }
}
