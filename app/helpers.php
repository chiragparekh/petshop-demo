<?php

if (! function_exists('format_number')) {
    function format_number($value) {
        return number_format($value, 2);
    }
}