<?php

// Removes unused image sizes
function disable_core_image_sizes() {
    remove_image_size('medium_large');
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
}
add_action('init', 'disable_core_image_sizes');

function disable_core_image_sizes_settings($sizes) {
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'disable_core_image_sizes_settings');

add_filter('intermediate_image_sizes', function($sizes) {
    return array_diff($sizes, ['medium_large']);
});

// IF ACF
/*function remove_unused_image_sizes_from_acf_wysiwyg($sizes) {
    unset($sizes['medium_large']);
    unset($sizes['large']);
    unset($sizes['thumbnail']);
    
    return $sizes;
}
add_filter('image_size_names_choose', 'remove_unused_image_sizes_from_acf_wysiwyg');*/

// Thumbnail image size needs to be set as 0 width/height at /wp-admin/options-media.php
