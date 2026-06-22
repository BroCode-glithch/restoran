<?php

if (!function_exists('base_url')) {
    function base_url($path = '') {
        return url($path);
    }
}