<?php

// Enqueue scripts and localize data
function mn_enqueue_custom_scripts() {
    wp_enqueue_script( 'custom-script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array( 'jquery' ) );
    wp_localize_script( 'custom-script', 'ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'add_product_to_order_nonce' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'mn_enqueue_custom_scripts' );

// Enqueue admin styles and scripts
function mn_enqueue_admin_scripts($hook) {
    if($hook != 'toplevel_page_after-sale-manager') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'admin-scripts',
        plugin_dir_url(__FILE__) . 'assets/js/admin.js',
        array('jquery', 'wp-color-picker'),
        false,
        true
    );
}
add_action('admin_enqueue_scripts', 'mn_enqueue_admin_scripts');