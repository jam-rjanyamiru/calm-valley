<?php

namespace EasyBooking;

/**
*
* Checkout action hooks and filters.
* @version 2.3.0
*
**/

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Checkout' ) ) :

class Checkout {

    public function __construct() {

        // Add booking dates to order item.
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_booking_data' ), 10, 3 );

        // Display formatted dates in checkout page.
        add_filter( 'woocommerce_display_item_meta', array( $this, 'display_booking_dates_in_checkout' ), 10, 3 );

    }

    /**
    *
    * Add booking dates and status to the order item meta data.
    *
    * @param int - $item_id
    * @param str - $cart_item_key
    * @param array - $values
    *
    **/
    public function add_order_item_booking_data( $item, $cart_item_key, $values ) {

        if ( ! empty( $values['_booking_start_date'] ) ) {

            // Start date format yyyy-mm-dd
            $start = sanitize_text_field( $values['_booking_start_date'] );

            // Store start date. 
            $item->add_meta_data( '_booking_start_date', $start );

            // End date format yyyy-mm-dd
            $end = ! empty( $values['_booking_end_date'] ) ? sanitize_text_field( $values['_booking_end_date'] ) : false;

            // Maybe store end date.
            if ( $end ) {
                $item->add_meta_data( '_booking_end_date', $end );
            }
            
            // Backward compatibility 2.3.0
            do_action_deprecated( 'easy_booking_add_booked_item', array( $item, $start, $end ), '2.3.0', 'easy_booking_add_order_item_booking_data' );

            do_action( 'easy_booking_add_order_item_booking_data', $item, $start, $end );

        }

    }

    

    /**
    *
    * Display order item localized booking dates in checkout.
    *
    * @param str $output
    * @param WC_Order_Item $order_item
    * @param str $output
    *
    **/
    public function display_booking_dates_in_checkout( $html, $item, $args ) {

        $product = $item->get_product();

        $start_text = esc_html( wceb_get_start_text( $product ) );
        $end_text   = esc_html( wceb_get_end_text( $product ) );

        $start = $item->get_meta( '_booking_start_date' );

        if ( isset( $start ) && ! empty( $start ) ) {
            $formatted_start = date_i18n( get_option( 'date_format' ), strtotime( $start ) );
            $html .= '<dl class="variation">' . wp_kses_post( $start_text . ': ' . $formatted_start ) . '</dl>';
        }

        $end = $item->get_meta( '_booking_end_date' );

        if ( isset( $end ) && ! empty( $end ) ) {
            $formatted_end = date_i18n( get_option( 'date_format' ), strtotime( $end ) );
            $html .= '<dl class="variation">' . wp_kses_post( $end_text . ': ' . $formatted_end ) . '</dl>';
        }

        return $html;

    }

}

return new Checkout();

endif;