<?php

if (!function_exists('relative_route')) {
    /**
     * Generate a relative URL to a named route.
     *
     * @param string $name
     * @param null|array $parameters
     * @return string
     */
    function relative_route(string $name, null|array $parameters = []): string
    {
        return app('url')->route($name, $parameters, false);
    }
}