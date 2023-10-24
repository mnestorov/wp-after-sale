<?php

// Customize Thank You page content
function mn_custom_thank_you_page_content( $order_id ) {
    $order = wc_get_order( $order_id );
    // Check if order exists and payment method is Cash on Delivery
    if ( ! $order || $order->get_payment_method() != 'cod' ) return;
    
    // Change order status to "On Hold"
    $order->update_status('on-hold');

    $categories = array();
    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();
        $product_categories = wp_get_post_terms( $product->get_id(), 'product_cat', array( "fields" => "ids" ) );
        $categories = array_merge( $categories, $product_categories );
    }
    
    $categories = array_unique( $categories );

    // Display additional products
    echo do_shortcode('[products category="' . implode(',', $categories) . '" limit="4" columns="4"]');
}
add_action( 'woocommerce_thankyou', 'mn_custom_thank_you_page_content' );

// Define a new shortcode to display products commonly purchased together
function mn_customers_also_bought_shortcode( $atts, $content = null ) {
    // Access the global $wp object to get query variables
    global $wp;
    
    // Get the order ID from the URL query vars (assumes the shortcode is used on the WooCommerce Thank You page)
    $order_id = $wp->query_vars['order-received'];
    
    // Get the order object using the order ID
    $order = wc_get_order( $order_id );

    // If there's no order object, return an empty string to exit the function
    if ( ! $order ) return '';

    // Initialize an empty array to hold the categories of the products in the order
    $categories = array();

    // Loop through each item in the order
    foreach ( $order->get_items() as $item ) {
        // Get the product object for the current item
        $product = $item->get_product();
        
        // Get the categories of the current product, retrieving only the category IDs
        $product_categories = wp_get_post_terms( $product->get_id(), 'product_cat', array( "fields" => "ids" ) );
        
        // Merge the current product's categories into the $categories array
        $categories = array_merge( $categories, $product_categories );
    }

    // Remove duplicate category IDs from the $categories array
    $categories = array_unique( $categories );

    // Return a WooCommerce products shortcode to display products from the same categories as the products in the order
    // The 'category' attribute is set to a comma-separated list of category IDs
    // The 'limit' attribute is set to 4 to limit the display to 4 products
    // The 'columns' attribute is set to 4 to display the products in 4 columns
    return do_shortcode('[products category="' . implode(',', $categories) . '" limit="4" columns="4"]');
}
add_shortcode( 'customers_also_bought', 'mn_customers_also_bought_shortcode' );