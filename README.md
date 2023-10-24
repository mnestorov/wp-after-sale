<p align="center"><a href="https://wordpress.org" target="_blank"><img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/wordpress/wordpress.png" width="100" alt="WordPress Logo"></a></p>

# WordPress - After-Sale Manager

[![Licence](https://img.shields.io/github/license/Ileriayo/markdown-badges?style=for-the-badge)](./LICENSE)

## Overview

A custom plugin to show additional products (after-sale) on the Thank You page and allow users to add them to their order if the payment method is Cash on Delivery (CoD).

## Features

- Display additional products on the Thank You page.
- Allow users to add extra products to their order if the payment method is Cash on Delivery.
- Apply bundle deals based on the products added to the order.
- Offer discounts for adding certain additional products to the order.
- Customize the appearance of upsell products.
- All functionalities are customizable via the WordPress admin dashboard.

## Installation

1. Download the plugin from the repository.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to 'After-Sale Manager' in the WordPress admin menu to configure the plugin settings.

## Usage

After installing and activating the plugin, navigate to the After-Sale Manager settings page to configure the plugin according to your needs. Set up bundle deals, discounts, and upsell styles to customize the user experience on the Thank You page.

## Functions

- `mn_enqueue_custom_scripts()`: Enqueue scripts and localize data.
- `mn_register_settings_page()`: Register a settings page.
- `mn_render_settings_page()`: Render the settings page.
- `mn_register_settings()`: Register settings and sections.
- `mn_render_bundle_deals_section()`: Render fields for managing bundle deals.
- `mn_render_upsell_styles_section()`: Render fields for customizing upsell styles.
- `mn_register_discount_settings()`: Register settings for discounts.
- `mn_render_discounts_section()`: Render discounts section.
- `mn_add_product_to_order()`: Add product to order and update order status.
- `mn_enqueue_admin_scripts()`: Enqueue admin styles and scripts.
- `mn_custom_thank_you_page_content()`: Customize Thank You page content.
- `mn_customers_also_bought_shortcode()`: Define a new shortcode to display products commonly purchased together.
- `mn_apply_bundle_deals()`: Apply bundle deals with the data from the plugin settings.
- `mn_ajax_add_product_to_order()`: AJAX action to add product to order.

## Error Handling and Debugging

Enable `WP_DEBUG` in your WordPress configuration to catch errors during development. In your `wp-config.php` file:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

## Uninstallation

1. Navigate to the 'Plugins' menu in WordPress.
2. Locate the plugin and click on 'Deactivate'.
3. After deactivating, click on 'Delete' to remove the plugin and its data from your site.

## Changelog

For a detailed list of changes and updates made to this project, please refer to our [Changelog](./CHANGELOG.md).

---

## License

This project is released under the MIT License.
