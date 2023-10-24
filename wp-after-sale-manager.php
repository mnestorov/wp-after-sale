<?php
/**
 * Plugin Name: MN - WordPress After-Sale Manager
 * Plugin URI: https://github.com/mnestorov/wp-after-sale-manager
 * Description: A custom plugin to show additional products on the Thank You page and allow users to add them to their order if the payment method is Cash on Delivery.
 * Version: 1.0
 * Author: Martin Nestorov
 * Author URI: https://github.com/mnestorov
 * Text Domain: mn-wordpress-after-sale-manager
 * Tags: wp, wp-plugin, wp-admin, wordpress, wordpress-plugin, wordpress-cookie, wordpress-multisite
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue scripts and localize data
function enqueue_custom_scripts() {
    wp_enqueue_script( 'custom-script', plugin_dir_url( __FILE__ ) . 'script.js', array( 'jquery' ) );
    wp_localize_script( 'custom-script', 'ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'add_product_to_order_nonce' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_scripts' );

// Customize Thank You page content
function custom_thank_you_page_content( $order_id ) {
    $order = wc_get_order( $order_id );
    // Check if order exists and payment method is Cash on Delivery
    if ( ! $order || $order->get_payment_method() != 'cod' ) return;

    // Change order status to "On Hold"
    $order->update_status('on-hold');

    // Display additional products
    echo do_shortcode('[products limit="4" columns="4" orderby="popularity"]');
}
add_action( 'woocommerce_thankyou', 'custom_thank_you_page_content' );

// AJAX action to add product to order
function ajax_add_product_to_order() {
    // Verify nonce for security
    if ( ! wp_verify_nonce( $_POST['nonce'], 'add_product_to_order_nonce' ) ) {
        wp_send_json_error( 'Nonce verification failed' );
        wp_die();
    }

    // Sanitize and validate input data
    $order_id = absint( $_POST['order_id'] );
    $product_id = absint( $_POST['product_id'] );
    $quantity = absint( $_POST['quantity'] );

    // Validate data
    if ( ! $order_id || ! $product_id || ! $quantity ) {
        wp_send_json_error( 'Invalid data' );
        wp_die();
    }

    // Add product to order and update order status
    add_product_to_order( $order_id, $product_id, $quantity );

    wp_die();
}
add_action( 'wp_ajax_add_product_to_order', 'ajax_add_product_to_order' );

// Function to add product to order and update order status
function add_product_to_order( $order_id, $product_id, $quantity ) {
    $order = wc_get_order( $order_id );
    // Check if order exists and payment method is Cash on Delivery
    if ( ! $order || $order->get_payment_method() != 'cod' ) return;

    // Add product to order
    $order->add_product( wc_get_product( $product_id ), $quantity );
    $order->calculate_totals();
    
    // Update order status to "Processing"
    $order->update_status('processing');
}
