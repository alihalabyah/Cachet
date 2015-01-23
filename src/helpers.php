<?php

use CachetHQ\Segment\Facades\Segment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

if (!function_exists('elixir')) {
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param string $file
     *
     * @return string
     */
    function elixir($file)
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifest = json_decode(file_get_contents(public_path().'/build/rev-manifest.json'), true);
        }

        if (isset($manifest[$file])) {
            return '/build/'.$manifest[$file];
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|void $key
     * @param mixed             $default
     *
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if ($key === null) {
            return Config::getItems();
        }

        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                Config::set($innerKey, $innerValue);
            }

            return;
        }

        return Config::get($key, $default);
    }
}

if (!function_exists('set_active')) {

    /**
     * Set active class if request is in path.
     *
     * @param string $path
     * @param array  $classes
     * @param string $active
     *
     * @return string
     */
    function set_active($path, array $classes = [], $active = 'active')
    {
        if (Request::is($path)) {
            $classes[] = $active;
        }

        $class = implode(' ', $classes);

        return empty($classes) ? '' : "class=\"{$class}\"";
    }
}

if (!function_exists('segment_identify')) {
    /**
     * Identifies the user for Segment.com.
     *
     * @return bool
     */
    function segment_identify()
    {
        if (Setting::get('app_track')) {
            return Segment::identify([
                'userId' => Config::get('app.key'),
            ]);
        } else {
            return false;
        }
    }
}

if (!function_exists('segment_track')) {
    /**
     * Tracks events in Segment.com.
     *
     * @param string $event
     * @param array  $properties
     *
     * @return bool
     */
    function segment_track($event, array $properties)
    {
        if (Setting::get('app_track')) {
            segment_identify(Config::get('app.key'));

            return Segment::track([
                'userId'     => Config::get('app.key'),
                'event'      => $event,
                'properties' => $properties,
            ]);
        } else {
            return false;
        }
    }
}

if (!function_exists('segment_page')) {
    /**
     * Tracks pages in Segment.com.
     *
     * @param string $name
     *
     * @return bool
     */
    function segment_page($page)
    {
        if (Setting::get('app_track')) {
            segment_identify(Config::get('app.key'));

            return Segment::page([
                'userId' => Config::get('app.key'),
                'page'   => $page,
            ]);
        } else {
            return false;
        }
    }
}
