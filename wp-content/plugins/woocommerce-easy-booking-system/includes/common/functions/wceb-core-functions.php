<?php

defined( 'ABSPATH' ) || exit;

/**
*
* Query orders with the right statuses.
*
* @param bool | $past | False to get only "processing" orders, true to get all orders.
* @return array
*
**/
function wceb_get_orders( $past = true ) {

	// Query orders
    $args = array(
        'post_type'      => 'shop_order',
        'post_status'    => apply_filters( 
                            'easy_booking_get_order_statuses',
                            array(
                                'wc-pending',
                                'wc-processing',
                                'wc-on-hold',
                                'wc-completed',
                                'wc-refunded'
                            ) ),
        'posts_per_page' => -1,
        'fields'         => 'ids'
    );

    if ( ! $past ) {
        $args['meta_key']   = 'order_booking_status';
        $args['meta_value'] = 'processing';
    }

    $query_orders = new WP_Query( $args );

    return $query_orders->posts;

}

/**
*
* Gets all booked products from the existing orders.
*
* @return array $booked - An array of all booked products
*
**/
function wceb_get_booked_items_from_orders( $past = true ) {

    // Query orders
    $orders = wceb_get_orders( $past );

    $products = array();
    foreach ( $orders as $index => $order_id ) :

        $order = wc_get_order( $order_id );
        $items = $order->get_items();

        $data = array();
        if ( $items ) foreach ( $items as $item_id => $item ) {

            $product_id   = $item['product_id'];
            $variation_id = $item['variation_id'];

            $product = is_a( $item, 'WC_Order_Item_Product' ) ? $item->get_product() : false;

            if ( ! $product ) {
                continue;
            }

            if ( wceb_is_bookable( $product ) ) {

                // If start date is set (backward compatibility 2.3.0: _ebs_start_format depreacted)
                if ( isset( $item['_booking_start_date'] ) || isset( $item['_ebs_start_format'] ) ) {

                    $id    = empty( $variation_id ) || $variation_id === '0' ? $product_id : $variation_id;
                    $start = isset( $item['_booking_start_date'] ) ? $item['_booking_start_date'] : $item['_ebs_start_format'];

                    // Check date format to avoid errors (yyyy-mm-dd) and check if product or variation still exists
                    if ( ! wceb_is_valid_date( $start ) || ! wc_get_product( $id ) ) {
                        continue;
                    }

                    $quantity = intval( $item['qty'] );

                    // If a refund of the product has been made, get the refunded quantity
                    $refunded_qty = $order->get_qty_refunded_for_item( $item_id );

                    // Removed refunded items
                    if ( $refunded_qty > 0 ) {
                        $quantity = $quantity - $refunded_qty;
                    }

                    // If 0 items are left, return
                    if ( $quantity <= 0 ) {
                        continue;
                    }

                    $status = isset( $item['_booking_status'] ) ? esc_html( $item['_booking_status'] ) : 'wceb-pending';

                    $data = array(
                        'product_id' => $id,
                        'order_id'   => $order_id,
                        'start'      => $start,
                        'qty'        => $quantity,
                        'status'     => $status
                    );

                }

                // If end date is set (backward compatibility 2.3.0: _ebs_end_format depreacted)
                if ( ( isset( $item['_booking_end_date'] ) && ! empty( $item['_booking_end_date'] ) ) || ( isset( $item['_ebs_end_format'] ) && ! empty( $item['_ebs_end_format'] ) ) ) {
                    
                    $end = isset( $item['_booking_end_date'] ) ? $item['_booking_end_date'] : $item['_ebs_end_format'];

                    // Check date format to avoid errors (yyyy-mm-dd)
                    if ( ! wceb_is_valid_date( $end ) ) {
                        continue;
                    }

                    $data['end'] = $end;

                }

                if ( ! empty( $data ) && isset( $data['product_id'] ) ) {
                    $products[] = apply_filters( 'easy_booking_booked_reports', $data );
                }

            }
        
        }

    endforeach;
    
    // Sort array by product IDs
    usort( $products, 'wceb_sort_by_product_id' );
    
    return $products;

}