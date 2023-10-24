<?php
/**
 * Plugin Name: MN - WordPress After-Sale Manager
 * Plugin URI: https://github.com/mnestorov/wp-after-sale-manager
 * Description: A custom plugin to show additional products on the Thank You page and allow users to add them to their order if the payment method is Cash on Delivery.
 * Version: 1.3
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

// Register a Settings Page
function register_settings_page() {
    add_menu_page(
        'After-Sale Manager',
        'After-Sale Manager',
        'manage_options',
        'after-sale-manager',
        'render_settings_page'
    );
}
add_action('admin_menu', 'register_settings_page');

// Render the Settings Page
function render_settings_page() {
    ?>
    <div class="wrap">
        <h1>After-Sale Manager Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('after_sale_manager_settings');
            do_settings_sections('after-sale-manager');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register Settings and Sections
function register_settings() {
    register_setting('after_sale_manager_settings', 'asm_bundle_deals');
    register_setting('after_sale_manager_settings', 'asm_upsell_styles');

    add_settings_section(
        'bundle_deals_section',
        'Bundle Deals',
        'render_bundle_deals_section',
        'after-sale-manager'
    );

    add_settings_section(
        'upsell_styles_section',
        'Upsell Styles',
        'render_upsell_styles_section',
        'after-sale-manager'
    );
}
add_action('admin_init', 'register_settings');

// Rendering Fields for Managing Bundle Deals
function render_bundle_deals_section() {
    // Get saved bundle deals
    $bundle_deals = get_option('asm_bundle_deals', array());

    echo '<div id="bundle-deals-section">';

    foreach ($bundle_deals as $index => $bundle) {
        echo '<div class="bundle-deal">';
        echo '<input type="number" name="asm_bundle_deals[' . $index . '][product_id]" value="' . esc_attr($bundle['product_id']) . '" placeholder="Product ID" />';
        echo '<input type="number" name="asm_bundle_deals[' . $index . '][free_product_id]" value="' . esc_attr($bundle['free_product_id']) . '" placeholder="Free Product ID" />';
        echo '<input type="number" name="asm_bundle_deals[' . $index . '][free_quantity]" value="' . esc_attr($bundle['free_quantity']) . '" placeholder="Free Quantity" />';
        echo '</div>';
    }

    echo '</div>';
    echo '<button type="button" id="add-bundle-deal">Add Bundle Deal</button>';
}

// Rendering Fields for Customizing Upsell Styles
function render_upsell_styles_section() {
    // Get saved upsell styles
    $upsell_styles = get_option('asm_upsell_styles', array(
        'background_color' => '#ffffff',
        'text_color' => '#000000',
        'border_color' => '#cccccc'
    ));

    echo '<div id="upsell-styles-section">';
    echo '<input type="text" name="asm_upsell_styles[background_color]" value="' . esc_attr($upsell_styles['background_color']) . '" class="color-field" data-default-color="#ffffff" />';
    echo '<input type="text" name="asm_upsell_styles[text_color]" value="' . esc_attr($upsell_styles['text_color']) . '" class="color-field" data-default-color="#000000" />';
    echo '<input type="text" name="asm_upsell_styles[border_color]" value="' . esc_attr($upsell_styles['border_color']) . '" class="color-field" data-default-color="#cccccc" />';
    echo '</div>';
}

// Enqueue Admin Styles and Scripts
function enqueue_admin_scripts($hook) {
    if($hook != 'toplevel_page_after-sale-manager') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'admin-scripts',
        plugin_dir_url(__FILE__) . 'admin.js',
        array('jquery', 'wp-color-picker'),
        false,
        true
    );
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');


// Customize Thank You page content
function custom_thank_you_page_content( $order_id ) {
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
add_action( 'woocommerce_thankyou', 'custom_thank_you_page_content' );

// Define a new shortcode to display products commonly purchased together
function customers_also_bought_shortcode( $atts, $content = null ) {
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
add_shortcode( 'customers_also_bought', 'customers_also_bought_shortcode' );

function apply_bundle_deals( $order_id, $product_id, $quantity ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        error_log( "Order not found: $order_id" );
        return;
    }

    // Define bundle deals
    // Example: Buy product ID 10 and get product ID 20 for free
    $bundle_deals = array(
        10 => array(
            'free_product_id' => 20,
            'free_quantity' => 1
        )
        // ... more bundle deals
    );

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
function ajax_add_product_to_order() {
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

add_action( 'wp_ajax_add_product_to_order', 'ajax_add_product_to_order' );

// Function to add product to order and update order status
function add_product_to_order( $order_id, $product_id, $quantity ) {
    $order = wc_get_order( $order_id );
    // Check if order exists and payment method is Cash on Delivery
    if ( ! $order || $order->get_payment_method() != 'cod' ) {
        error_log( "Order not found or payment method is not COD: $order_id" );
        return;
    }
    
    // Add product to order
    $order->add_product( wc_get_product( $product_id ), $quantity );
    $order->calculate_totals();
    
    // Update order status to "Processing"
    $order->update_status('processing');

    // Apply bundle deals
    apply_bundle_deals( $order_id, $product_id, $quantity );
}
