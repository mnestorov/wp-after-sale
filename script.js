jQuery(document).ready(function($) {
    // Timer to disable the "Add to Order" button after 5 minutes
    let timer = 300;  // 5 minutes in seconds
    let intervalId = setInterval(() => {
        timer--;
        if (timer <= 0) {
            clearInterval(intervalId);
            $('.add-to-order-button').prop('disabled', true);
        }
    }, 1000);

    // Event handler for "Add to Order" button
    $('.add-to-order-button').on('click', function() {
        var product_id = $(this).data('product-id');
        var order_id = $(this).data('order-id');
        var quantity = 1;  // or get quantity from input field

        $.post(ajax_object.ajax_url, {
            action: 'add_product_to_order',
            order_id: order_id,
            product_id: product_id,
            quantity: quantity,
            nonce: ajax_object.nonce
        }, function(response) {
            if ( response.success ) {
                alert('Product added to order successfully.');
            } else {
                alert(response.data);
            }
        });
    });
});
