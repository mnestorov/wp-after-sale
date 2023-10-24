jQuery(document).ready(function($){
    $('.color-field').wpColorPicker();
});

jQuery(document).ready(function($) {
    $('#add-discount').on('click', function() {
        var discountIndex = $('#discounts-section .discount').length;
        var discountHTML = '<div class="discount">' +
            '<input type="number" name="mn_asm_discounts[' + discountIndex + '][product_id]" value="" placeholder="Product ID" />' +
            '<input type="number" name="mn_asm_discounts[' + discountIndex + '][discount_amount]" value="" placeholder="Discount Amount" />' +
            '</div>';
        
        $('#discounts-section').append(discountHTML);
    });
});

document.addEventListener('DOMContentLoaded', (event) => {
    // Get the 'Add Bundle Deal' button
    const addBundleDealButton = document.getElementById('add-bundle-deal');

    // Listen for clicks on the 'Add Bundle Deal' button
    addBundleDealButton.addEventListener('click', () => {
        // Get the bundle deals section
        const bundleDealsSection = document.getElementById('bundle-deals-section');

        // Get the current number of bundle deals
        const currentBundleDeals = bundleDealsSection.getElementsByClassName('bundle-deal').length;

        // Create a new bundle deal div
        const newBundleDealDiv = document.createElement('div');
        newBundleDealDiv.className = 'bundle-deal';

        // Create the input fields for the new bundle deal
        const productIdInput = document.createElement('input');
        productIdInput.type = 'number';
        productIdInput.name = `asm_bundle_deals[${currentBundleDeals}][product_id]`;
        productIdInput.placeholder = 'Product ID';

        const freeProductIdInput = document.createElement('input');
        freeProductIdInput.type = 'number';
        freeProductIdInput.name = `asm_bundle_deals[${currentBundleDeals}][free_product_id]`;
        freeProductIdInput.placeholder = 'Free Product ID';

        const freeQuantityInput = document.createElement('input');
        freeQuantityInput.type = 'number';
        freeQuantityInput.name = `asm_bundle_deals[${currentBundleDeals}][free_quantity]`;
        freeQuantityInput.placeholder = 'Free Quantity';

        // Append the input fields to the new bundle deal div
        newBundleDealDiv.appendChild(productIdInput);
        newBundleDealDiv.appendChild(freeProductIdInput);
        newBundleDealDiv.appendChild(freeQuantityInput);

        // Append the new bundle deal div to the bundle deals section
        bundleDealsSection.appendChild(newBundleDealDiv);
    });
});
