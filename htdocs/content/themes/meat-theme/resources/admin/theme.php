<?php

/**
 * Define your theme custom code.
 */

function my_activation_settings($theme)
{
    var_dump($theme);
    die;
    if ( 'meat-theme' == $theme )
    {
        die('me activé');
    }
    return;
}
add_action('switch_theme', 'my_activation_settings');