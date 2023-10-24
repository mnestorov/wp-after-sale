<?php

// Register a Settings Page
function mn_register_settings_page() {
    add_menu_page(
        'After-Sale Manager',
        'After-Sale Manager',
        'manage_options',
        'after-sale-manager',
        'render_settings_page'
    );
}
add_action('admin_menu', 'mn_register_settings_page');

// Render the Settings Page
function mn_render_settings_page() {
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
function mn_register_settings() {
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
add_action('admin_init', 'mn_register_settings');

// Rendering Fields for Managing Bundle Deals
function mn_render_bundle_deals_section() {
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
function mn_render_upsell_styles_section() {
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

// Register Settings for Discounts
function mn_register_discount_settings() {
    register_setting('mn_after_sale_manager_settings', 'mn_asm_discounts');

    add_settings_section(
        'mn_discounts_section',
        'Discounts',
        'mn_render_discounts_section',
        'mn_after-sale-manager'
    );
}
add_action('admin_init', 'mn_register_discount_settings');

// Render Discounts Section
function mn_render_discounts_section() {
    $discounts = get_option('mn_asm_discounts', array());

    echo '<div id="discounts-section">';
    foreach ($discounts as $index => $discount) {
        echo '<div class="discount">';
        echo '<input type="number" name="mn_asm_discounts[' . $index . '][product_id]" value="' . esc_attr($discount['product_id']) . '" placeholder="Product ID" />';
        echo '<input type="number" name="mn_asm_discounts[' . $index . '][discount_amount]" value="' . esc_attr($discount['discount_amount']) . '" placeholder="Discount Amount" />';
        echo '</div>';
    }
    echo '</div>';
    echo '<button type="button" id="add-discount">Add Discount</button>';
}