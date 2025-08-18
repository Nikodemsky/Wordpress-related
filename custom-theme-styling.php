<?php

/*********** CUSTOM STYLING ***********/

// Template styles
function wg_styles() {

    // Globals
    $theme_dir = get_stylesheet_directory_uri();

    // Register and enqueue styles
    wp_register_style( 'css-normalize-substrate-system', $theme_dir . '/assets/css/css-normalize.css', array(), '03.2025' ); // https://www.joshwcomeau.com/css/custom-css-reset/
    wp_register_style( 'utilities', $theme_dir . '/assets/css/utilities.css', array(), '1.1' );
    wp_register_style( 'wg-css', $theme_dir . '/assets/css/wg.css', array(), '1.00' ); 
    wp_register_style( 'responsive-767', $theme_dir . '/assets/css/responsive-767.css', array(), '1.00' ); 
    wp_register_style( 'responsive-1024', $theme_dir . '/assets/css/responsive-1024.css', array(), '1.00' ); 
    //wp_register_style( 'responsive-1279', $theme_dir . '/responsive-1279.css', array(), '1.00' ); 

    wp_enqueue_style( 'css-normalize-substrate-system' );
    wp_enqueue_style( 'utilities' );
    wp_enqueue_style( 'wg-css' );
    wp_enqueue_style( 'responsive-767' );
    wp_enqueue_style( 'responsive-1024' );
    //wp_enqueue_style( 'responsive-1279' );

}
add_action( 'wp_enqueue_scripts', 'wg_styles' );
