function wp_stop_heartbeat() {
  wp_deregister_script('heartbeat');
}
add_action( 'init', 'wp_stop_heartbeat', 1 );
