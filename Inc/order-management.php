<?php

// Modify the add_product_to_order function to include discount application
function mn_add_product_to_order( $order_id, $product_id, $quantity ) {
    $order = wc_get_order( $order_id );
    if ( ! $order || $order->get_payment_method() != 'cod' ) {
        error_log( "Order not found or payment method is not COD: $order_id" );
        return false;
    }
    
    // Get the product and discount settings
    $product = wc_get_product( $product_id );
    $discounts = get_option('mn_asm_discounts', array());
    
    // Check if a discount applies to this product
    foreach ($discounts as $discount) {
        if ($discount['product_id'] == $product_id) {
            $discount_amount = $discount['discount_amount'];
            $product_price = $product->get_price() - $discount_amount;
            $product->set_price($product_price);  // Set new price with discount
        }
    }

    // Add product to order
    $order->add_product( $product, $quantity );
    $order->calculate_totals();
    
    // Update order status to "Processing"
    $order->update_status('processing');

    // Apply bundle deals
    mn_apply_bundle_deals( $order_id, $product_id, $quantity );

    return true;
}

// Apply Bundle Deals With the Data From the Plugin Settings
function mn_apply_bundle_deals( $order_id, $product_id, $quantity ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        error_log( "Order not found: $order_id" );
        return;
    }

    // Get bundle deals from the plugin settings
    $bundle_deals_option = get_option('asm_bundle_deals', array());

    // Convert the settings array to a format that's easy to use in the function
    $bundle_deals = array();
    foreach ($bundle_deals_option as $bundle_deal) {
        $bundle_deals[$bundle_deal['product_id']] = array(
            'free_product_id' => $bundle_deal['free_product_id'],
            'free_quantity' => $bundle_deal['free_quantity']
        );
    }

    // Check if the added product triggers a bundle deal
    if ( isset( $bundle_deals[ $product_id ] ) ) {
        $bundle_deal = $bundle_deals[ $product_id ];
        $free_product_id = $bundle_deal['free_product_id'];
        $free_quantity = $bundle_deal['free_quantity'];

        // Add the free product to the order
        $order->add_product( wc_get_product( $free_product_id ), $free_quantity );
        $order->calculate_totals();
    }
}

// AJAX action to add product to order
function mn_ajax_add_product_to_order() {
    // Verify nonce for security
    if ( ! wp_verify_nonce( $_POST['nonce'], 'add_product_to_order_nonce' ) ) {
        wp_send_json_error( 'Nonce verification failed', 400 );
        wp_die();
    }

    // Sanitize and validate input data
    $order_id = absint( $_POST['order_id'] );
    $product_id = absint( $_POST['product_id'] );
    $quantity = absint( $_POST['quantity'] );

    // Validate data
    if ( ! $order_id || ! $product_id || ! $quantity ) {
        wp_send_json_error( 'Invalid data', 400 );
        wp_die();
    }

    // Try to add the product to the order
    $success = add_product_to_order( $order_id, $product_id, $quantity );
    if ( ! $success ) {
        wp_send_json_error( 'Failed to add product to order', 500 );
        wp_die();
    }

    // Try to apply bundle deals
    $success = apply_bundle_deals( $order_id, $product_id, $quantity );  // Apply bundle deals
    if ( ! $success ) {
        wp_send_json_error( 'Failed to apply bundle deals', 500 );
        wp_die();
    }

    wp_send_json_success( 'Product added and bundle deals applied successfully' );
    wp_die();
}
add_action( 'wp_ajax_add_product_to_order', 'mn_ajax_add_product_to_order' );