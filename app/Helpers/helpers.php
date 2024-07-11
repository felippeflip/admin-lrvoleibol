<?php

use Illuminate\Support\Carbon;

if (!function_exists('carbon')) {
    /**
     * Get a Carbon instance.
     *
     * @param  mixed  $time
     * @param  string|null  $tz
     * @return \Carbon\Carbon
     */
    function carbon($time = null, $tz = null)
    {
        return Carbon::parse($time, $tz);
    }
}