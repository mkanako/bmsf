<?php

if (!function_exists('succ')) {
    function succ(...$args)
    {
        return call_user_func_array([response(), __FUNCTION__], $args);
    }
}

if (!function_exists('err')) {
    function err(...$args)
    {
        return call_user_func_array([response(), __FUNCTION__], $args);
    }
}
