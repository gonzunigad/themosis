<?php

function my_acf_json_save_point($path){
    $path = themosis_path('theme') . 'acf-json';
    return $path;
}

function my_acf_json_load_point($paths){
    // remove original path (optional)
    unset($paths[0]);
    $paths[] = themosis_path('theme') . 'acf-json';
    return $paths;
}

# Saving explained
add_filter('acf/settings/save_json', 'my_acf_json_save_point');

# Loading Explained
add_filter('acf/settings/load_json', 'my_acf_json_load_point');