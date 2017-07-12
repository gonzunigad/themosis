<?php 
/*
 |--------------------------------------------------------------------------
 | Remove Emojis
 |--------------------------------------------------------------------------
 |
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

function my_deregister_scripts(){

wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'my_deregister_scripts' );