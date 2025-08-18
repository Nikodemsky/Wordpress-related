<?php

/*********** OPTIMIZATIONS ***********/

// Remove gutenberg styles
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' );
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

function dequeue_gutenberg_assets() {
    wp_dequeue_script('wp-editor');
}
add_action('wp_enqueue_scripts', 'dequeue_gutenberg_assets', 100);

// Disable emojis
function disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );    
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    
    // Remove from TinyMCE
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

function disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

// Removes comments completely
add_action('admin_init', function () {

    global $pagenow;
    
    if ($pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php') {
        wp_redirect(admin_url());
        exit;
    }

    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
    remove_submenu_page('options-general.php', 'options-discussion.php');
});

add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

function remove_comments(){
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'remove_comments' );

// Disable oEmbed on the website but keep it enabled for external platforms
function disable_oembed_on_site() {
    if (!is_admin()) {
        remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        add_filter( 'embed_oembed_discover', '__return_false' );
        add_filter( 'embed_preview', '__return_false' );
    }
}
add_action( 'init', 'disable_oembed_on_site' );

// Disable RSS feeds completely  
add_action('do_feed', 'wp_disable_feeds', 1);
add_action('do_feed_rdf', 'wp_disable_feeds', 1);
add_action('do_feed_rss', 'wp_disable_feeds', 1);
add_action('do_feed_rss2', 'wp_disable_feeds', 1);
add_action('do_feed_atom', 'wp_disable_feeds', 1);
add_action('do_feed_rss2_comments', 'wp_disable_feeds', 1);
add_action('do_feed_atom_comments', 'wp_disable_feeds', 1);

function wp_disable_feeds() {
    wp_die( __('No feeds available!') );
}

// Remove Widgets
function remove_widget_support() {
    remove_theme_support( 'widgets-block-editor' );
    remove_theme_support( 'widgets' );
}
add_action( 'after_setup_theme', 'remove_widget_support' );

// Disable Really Simple Discovery
remove_action( 'wp_head', 'rsd_link' );

// Remove Wordpress shortlink
remove_action('wp_head', 'wp_shortlink_wp_head', 10);

// Remove jQuery Migrate
function remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
            $script = $scripts->registered['jquery'];
    if ( $script->deps ) { 
    // Check whether the script has any dependencies
    $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
}}}
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );

// Remove admin dashboard widgets
function remove_all_dashboard_widgets() {

    // Remove Welcome Panel
    remove_action( 'welcome_panel', 'wp_welcome_panel' );

    // Remove Widgets
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // Quick Draft
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // Activity
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress News
    remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' ); // Site Health
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // At a Glance
}
add_action( 'wp_dashboard_setup', 'remove_all_dashboard_widgets' );

// Remove REST API links from header
function remove_json_api () {
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
add_action( 'after_setup_theme', 'remove_json_api' );

// Disable self-pingbacks
function no_self_ping( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );

// Disable built-in sitemap
add_filter('wp_sitemaps_enabled', '__return_false');

// Disable Help tabs in admin
add_filter( 'contextual_help', 'mytheme_remove_help_tabs', 999, 3 );
function mytheme_remove_help_tabs($old_help, $screen_id, $screen){
    $screen->remove_help_tabs();
    return $old_help;
}

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Modify heartbeat interval (in seconds)
add_filter( 'heartbeat_settings', 'modify_heartbeat_settings' );
function modify_heartbeat_settings( $settings ) {
    $settings['interval'] = 60; // 60 seconds instead of default 15
    return $settings;
}
