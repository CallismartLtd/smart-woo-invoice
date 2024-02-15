<?php
/**
 * File name    :   sw-order-config.php
 * @author      :   Callistus
 * Description  :   This file defines the checkout flow of Smart Woo Product
 */

/**
 * Add configured product data to cart item session.
 *
 * This function is hooked into 'woocommerce_add_cart_item_data' to include additional data
 * related to the configured product when adding it to the cart.
 *
 * @param array $cart_item_data The existing cart item data.
 * @param int $product_id The ID of the product being added to the cart.
 * @param int $variation_id The ID of the product variation being added to the cart.
 * @return array The modified cart item data with added service_name and service_url.
 */
add_filter( 'woocommerce_add_cart_item_data', 'sw_add_configured_product_to_cart', 10, 3 );

function sw_add_configured_product_to_cart( $cart_item_data, $product_id, $variation_id ) {
    // Check if 'service_name' is set in the POST data and add it to cart item data
    if ( isset( $_POST['service_name'] ) ) {
        $cart_item_data['sw_service_name'] = wc_clean( $_POST['service_name'] );
    }

    // Check if 'service_url' is set in the POST data and add it to cart item data
    if (isset( $_POST['service_url'] ) ) {
        $cart_item_data['sw_service_url'] = esc_url_raw( $_POST['service_url'] );
    }

    // Return the modified cart item data
    return $cart_item_data;
}


/**
 * Get configured product data from cart item session.
 *
 * This function is hooked into 'woocommerce_get_cart_item_from_session' to retrieve
 * additional data related to the configured product when reconstructing the cart item from session.
 *
 * @param array $cart_item The existing cart item data.
 * @param array $values The session data for the cart item.
 * @return array The modified cart item data with retrieved sw_service_name and sw_service_url.
 */
add_filter( 'woocommerce_get_cart_item_from_session', 'sw_get_configured_product_from_session', 10, 2 );

function sw_get_configured_product_from_session( $cart_item, $values ) {
    // Check if 'sw_service_name' is set in the session data and add it to cart item data
    if ( isset($values['sw_service_name'] ) ) {
        $cart_item['sw_service_name'] = $values['sw_service_name'];
    }

    // Check if 'sw_service_url' is set in the session data and add it to cart item data
    if ( isset( $values['sw_service_url'] ) ) {
        $cart_item['sw_service_url'] = $values['sw_service_url'];
    }

    // Return the modified cart item data
    return $cart_item;
}


/**
 * Display configured product data in cart and checkout.
 *
 * This function is hooked into 'woocommerce_get_item_data' to add custom data related to the
 * configured product for display in the cart and checkout.
 *
 * @param array $cart_data The existing cart item data.
 * @param array $cart_item The cart item being displayed.
 * @return array The modified cart item data with added service_name and service_url.
 */
add_filter( 'woocommerce_get_item_data', 'sw_display_configured_product_data_in_cart', 10, 2 );

function sw_display_configured_product_data_in_cart( $cart_data, $cart_item ) {
    // Check if 'sw_service_name' is set in the cart item data and add it to cart data for display
    if ( isset( $cart_item['sw_service_name'] ) ) {
        $cart_data[] = array(
            'name'    => '<div class="sw-configured-product-container"><strong>' . __( 'Service Name', 'smart-woo' ) . '</strong>',
            'value'   => '<span class="sw-configured-product">' . esc_html( $cart_item['sw_service_name'] ) . '</span></div>',
            'display' => '',
        );
    }

    // Check if 'sw_service_url' is set in the cart item data and add it to cart data for display
    if ( isset( $cart_item['sw_service_url'] ) ) {
        $cart_data[] = array(
            'name'    => '<div class="sw-configured-product-container"><strong>' . __( 'Service URL', 'smart-woo' ) . '</strong>',
            'value'   => '<span class="sw-configured-product">' . esc_html( $cart_item['sw_service_url']) . '</span></div>',
            'display' => '',
        );
    }

    // Return the modified cart data
    return $cart_data;
}




/**
 * Configure the order with the data the customer provided during product configuration,
 * and save it to order item meta.
 *
 * This function is hooked into 'woocommerce_checkout_create_order_line_item' to add
 * custom meta data related to the configured product to the order item.
 *
 * @param WC_Order_Item_Product $item The order item.
 * @param string $cart_item_key The key of the cart item.
 * @param array $values The session data for the cart item.
 * @param WC_Order $order The order object.
 */
add_action('woocommerce_checkout_create_order_line_item', 'sw_save_configured_product_data_to_order_item_meta', 10, 4);

function sw_save_configured_product_data_to_order_item_meta( $item, $cart_item_key, $values, $order ) {
    // Check if 'sw_service_name' is set in the cart item data and add it to order item meta
    if (isset($values['sw_service_name'])) {
        $item->add_meta_data( 'Service Name', $values['sw_service_name'], true );
    }

    // Check if 'sw_service_url' is set in the cart item data and add it to order item meta
    if ( isset( $values['sw_service_url'])) {
        $item->add_meta_data( 'Service URL', $values['sw_service_url'], true );
    }
}



