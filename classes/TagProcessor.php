<?php

namespace Winter\Catalogue\Classes;

/**
 * Catalogue Markdown tag processor.
 *
 * @package winter\catalogue
 * @author Alexey Bobkov, Samuel Georges
 */
class TagProcessor
{
    use \Winter\Storm\Support\Traits\Singleton;

    /**
     * @var array Cache of processing callbacks.
     */
    private $callbacks = [];

    /**
     * Registers a callback function that handles catalogue post markup.
     * The callback function should accept two arguments - the HTML string
     * generated from Markdown contents and the preview flag determining whether
     * the function should return a markup for the catalogue post preview form or for the
     * front-end.
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    public function processTags($markup, $preview)
    {
        foreach ($this->callbacks as $callback) {
            $markup = $callback($markup, $preview);
        }

        return $markup;
    }
}
