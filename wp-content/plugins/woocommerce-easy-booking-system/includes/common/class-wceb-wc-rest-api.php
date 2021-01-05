<?php

namespace EasyBooking;

/**
*
* WC Rest API action hooks and filters.
* @version 2.3.0
*
**/

defined( 'ABSPATH' ) || exit;

class WC_Rest_API {

	public function __construct() {

        add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'wc_rest_api_add_item_booking_dates' ), 10, 3 );

	}

    /**
    *
    * Returns start and (maybe) end date(s) when fetching orders with WooCommerce Rest API.
    *
    **/
    function wc_rest_api_add_item_booking_dates( $response, $object, $request ) {

        $order_data = $response->get_data();

        if ( isset( $order_data['line_items'] ) ) foreach ( $order_data['line_items'] as $key => $item ) {

            $start_date = wc_get_order_item_meta( $item['id'], '_booking_start_date' );
            $end_date   = wc_get_order_item_meta( $item['id'], '_booking_end_date' );

            // Backward compatibility 2.3.0
            if ( empty( $start_date ) ) {
                $start_date = wc_get_order_item_meta( $item['id'], '_ebs_start_format' );
            }

            // Backward compatibility 2.3.0
            if ( empty( $end_date ) ) {
                $end_date = wc_get_order_item_meta( $item['id'], '_ebs_end_format' );
            }

            if ( empty( $start_date ) ) {
                $order_data['line_items'][ $key ]['start_date'] = $start_date;
            }

            if ( empty( $end_date ) ) {
                $order_data['line_items'][ $key ]['end_date'] = $end_date;
            }

        }

        $response->data = $order_data;

        return $response;

    }

}

return new WC_Rest_API();