<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        return getenv($key) ? getenv('DB_PREFIX') : $default;
    }
}