<?php
/**
 * Plugin Name: MN - WordPress After-Sale Manager
 * Plugin URI: https://github.com/mnestorov/wp-after-sale-manager
 * Description: A custom plugin to show additional products on the Thank You page and allow users to add them to their order if the payment method is Cash on Delivery.
 * Version: 1.5
 * Author: Martin Nestorov
 * Author URI: https://github.com/mnestorov
 * Text Domain: mn-wordpress-after-sale-manager
 * Tags: wp, wp-plugin, wp-admin, wordpress, wordpress-plugin, wordpress-cookie, wordpress-multisite
 */

// Prevent direct access to the file
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Include segregated functionality files
require plugin_dir_path( __FILE__ ) . 'inc/enqueue-scripts.php';
require plugin_dir_path( __FILE__ ) . 'inc/admin-settings.php';
require plugin_dir_path( __FILE__ ) . 'inc/order-management.php';
require plugin_dir_path( __FILE__ ) . 'inc/thank-you-page.php';