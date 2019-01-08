<?php 
/**
 * 常用函数
 * 
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

function get_format_date($tm = null) {
    if ($tm) {
        return date('Y-m-d H:i:s', $tm);
    }
    return date('Y-m-d H:i:s');
}

function url_path_format($path) {
    $path_params = explode('/', $path);
    $new_path_params = [];

    foreach($path_params as $v) {
        $new_path_params[] = rawurlencode($v);
    }

    return implode('/', $new_path_params);
}

?>