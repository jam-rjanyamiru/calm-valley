<?php

namespace EasyBooking;

/**
*
* All functions related to date selection.
* @version 2.2.9
*
**/

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Date_Selection' ) ) :

class Date_Selection {

	public function __construct() {
	}

	/**
    *
    * Get and check selected booking duration after selecting dates.
    * @param str - $start
    * @param str - $end
    * @param WC_Product | WC_Product_Variation - $_product
    * @return int | WP_Error - $duration
    *
    **/
	public static function get_selected_booking_duration( $start, $end, $_product ) {

        // Make sure all datetimes are in the same timezone.
        date_default_timezone_set( 'UTC' );

        // Booking mode (Days or Nights)
        $booking_mode = get_option( 'wceb_booking_mode' );

        // Get current date to check fo valid dates.
        $current_date = strtotime( date( 'Y-m-d' ) );

        // Start time.
        $start_time = strtotime( $start );

        // One-date selection.
        if ( empty( $end ) ) {

            // Check that selected date is not in the past.
            if ( $start_time < $current_date ) {
                return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
            }

            // Booking duration is always 1 for one-date selection.
            return 1;

        }

        // End time.
        $end_time = strtotime( $end );

        // Return error if end date is before start date.
        if ( $end_time < $start_time ) {
            return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
        }
        
        // Check that selected dates are not in the past.
        if ( $start_time < $current_date || $end_time < $current_date ) {
            return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
        }

        // Get booking duration in days
        $duration = absint( ( $start_time - $end_time ) / 86400 );

        if ( $duration <= 0 ) {
            $duration = 1;
        }

        // If booking mode is days and calculation mode is set to "Days", add one day
        if ( $booking_mode === 'days' && ( $start != $end ) ) {
            $duration += 1 ;
        }

        $booking_duration = wceb_get_product_booking_duration( $_product );

        // If booking mode is weeks and duration is a multiple of 7
        if ( $booking_duration === 'weeks' ) {

            if ( $booking_mode === 'nights' && $duration % 7 === 0 ) { // If in weeks mode, check that the duration is a multiple of 7
                $duration /= 7;
            } else if ( $booking_mode === 'days' && $duration % 6 === 0 ) { // Or 6 in "Days" mode
                $duration /= 6;
            } else { // Otherwise return an error
                return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
            }
            
        } else if ( $booking_duration === 'custom' ) {

            $custom_booking_duration = wceb_get_product_custom_booking_duration( $_product );

            if ( $duration % $custom_booking_duration === 0 ) {
                $duration /= $custom_booking_duration;
            } else {
                return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
            }

        }

        // If empty or number of days is inferior to 0, return error
        if ( empty( $duration ) || $duration <= 0 ) {
            return new \WP_Error( 'easy_booking_invalid_dates', __( 'Please choose valid dates', 'woocommerce-easy-booking-system' ) );
        }

        return apply_filters( 'easy_booking_selected_booking_duration', $duration, $start, $end, $_product );

    }

    /**
    *
    * Get simple product booking price.
    * @param array - $data
    * @param WC_Product - $product
    * @param WC_Product - $_product
    * @param array - $children
    * @return array - $booking_data
    *
    **/
    public static function get_simple_product_booking_data( $data, $product, $_product, $children ) {

        $booking_data = array();
        $id = $_product->get_id();

        // Get product price and (if on sale) regular price
        foreach ( array( 'price', 'regular_price' ) as $price_type ) {

            $price = $_product->{'get_' . $price_type}();

            if ( $price === '' ) {
                continue;
            }

            ${'new_' . $price_type} = self::calculate_booking_price( $price, $data, $price_type, $product, $_product );

        }

        $data['new_price'] = $new_price;

        if ( isset( $new_regular_price ) && ! empty( $new_regular_price ) && ( $new_regular_price !== $new_price ) ) {
            $data['new_regular_price'] = $new_regular_price;
        }

        $booking_data[$id] = $data;

        return apply_filters( 'easy_booking_simple_product_booking_data', $booking_data, $product );

    }

    /**
    *
    * Get variable product booking price.
    * @param array - $data
    * @param WC_Product - $product
    * @param WC_Product_Variation - $_product
    * @param array - $children
    * @return array - $booking_data
    *
    **/
    public static function get_variable_product_booking_data( $data, $product, $_product, $children ) {
        $booking_data = self::get_simple_product_booking_data( $data, $product, $_product, $children );
        return apply_filters( 'easy_booking_variable_product_booking_data', $booking_data, $product, $_product );
    }

    /**
    *
    * Get grouped product booking price.
    * @param array - $data
    * @param WC_Product - $product
    * @param WC_Product | WC_Product_Variation - $_product
    * @param array - $children
    * @return array - $booking_data
    *
    **/
    public static function get_grouped_product_booking_data( $data, $product, $_product, $children ) {

        $booking_data = array();
        $new_price = 0;
        $new_regular_price = 0;

        $id = $_product->get_id();

        foreach ( $children as $child_id => $quantity ) {

            if ( $quantity <= 0 || ( $child_id === $id ) ) {
                continue;
            }

            $child = wc_get_product( $child_id );

             foreach ( array( 'price', 'regular_price' ) as $price_type ) {

                $price = $child->{'get_' . $price_type}();

                if ( empty( $price ) ) {
                    continue;
                }   

                // Multiply price by duration only if children is bookable
                ${'child_new_' . $price_type} = self::calculate_booking_price( $price, $data, $price_type, $product, $child );

            }

            $data['new_price'] = $child_new_price;

            if ( isset( $child_new_regular_price ) && ! empty( $child_new_regular_price ) ) {
                $data['new_regular_price'] = $child_new_regular_price;
            }

            // Store child booking data
            $booking_data[$child_id] = $data;

            $booking_data[$child_id]['quantity'] = $quantity;

            // Add child price to total price
            $new_price += wc_format_decimal( $child_new_price * $quantity );

            if ( isset( $child_new_regular_price ) ) {
                $new_regular_price += wc_format_decimal( $child_new_regular_price * $quantity );
            }

        }

        // Make sure to set parent product price to 0 and remove regular price (parent product has no price).
        $data['new_price'] = 0;
        unset( $data['new_regular_price'] );

        // Store parent product data
        $booking_data[$id] = $data;
        
        return apply_filters( 'easy_booking_grouped_product_booking_data', $booking_data, $product, $children );

    }
  
  	/**
    *
    * Get bundle product booking price.
    * @param array - $data
    * @param WC_Product - $product
    * @param WC_Product | WC_Product_Variation - $_product
    * @param array - $children
    * @return array - $booking_data
    *
    **/
    public static function get_bundle_product_booking_data( $data, $product, $_product, $children ) {

        $booking_data = array();

        $id = $_product->get_id();

        if ( ! empty( $children ) ) foreach ( $children as $child_id => $quantity ) {

            // Parent ID is in $children array for technical reasons, but is not a child.
            if ( $child_id === $id ) {
                continue;
            }

            $child = wc_get_product( $child_id );
            $bundled_item = class_exists( 'EasyBooking\Pb_Functions' ) ? Pb_Functions::get_corresponding_bundled_item( $product, $child ) : false;

            // Return if no bundled item or if quantity is 0
            if ( ! $bundled_item || $quantity <= 0 ) {
                continue;
            }

            if ( $bundled_item->is_priced_individually() ) {

                $child = wc_get_product( $child_id );

                foreach ( array( 'price', 'regular_price' ) as $price_type ) {

                    $price = $child->{'get_' . $price_type}();

                    if ( empty( $price ) ) {
                        continue;
                    }

                    // Maybe apply bundle discount.
                    $discount = $bundled_item->get_discount();

                    if ( isset( $discount ) && ! empty( $discount ) ) {
                        $price -= ( $price * $discount / 100 );
                    }

                    // Multiply price by duration only if product is bookable
                    ${'child_new_' . $price_type} = self::calculate_booking_price( $price, $data, $price_type, $product, $child );

                }

            } else { // Tweak for not individually priced bundled products
                
                $child_new_price = 0;
                $child_new_regular_price = 0;

            }

            $data['new_price'] = $child_new_price;
            $data['new_regular_price'] = isset( $child_new_regular_price ) ? $child_new_regular_price : 0;

            $booking_data[$child_id] = $data;

            // Store parent product
            $booking_data[$child_id]['grouped_by'] = $id;

            // Store child quantity
            $booking_data[$child_id]['quantity']   = $quantity;

        }

        // Get parent product price and (if on sale) regular price
        foreach ( array( 'price', 'regular_price' ) as $price_type ) {

            $price = $product->{'get_' . $price_type}();

            if ( empty( $price ) ) {
                continue;
            }

            ${'new_' . $price_type} = self::calculate_booking_price( $price, $data, $price_type, $product, $_product );

        }

        $data['new_price'] = isset( $new_price ) ? $new_price : 0;

        if ( isset( $new_regular_price ) && ! empty( $new_regular_price ) && ( $new_regular_price !== $new_price ) ) {
            $data['new_regular_price'] = $new_regular_price;
        } else {
            unset( $data['new_regular_price'] ); // Unset value in case it was set for a child product
        }

        $booking_data[$id] = $data;

        return apply_filters( 'easy_booking_bundle_product_booking_data', $booking_data, $product, $children );

    }

    /**
    *
    * Calculate product booking price.
    * @param str - $price
    * @param array - $data
    * @param str - $price_type
    * @param WC_Product - $product
    * @param WC_Product | WC_Product_Variation - $_product
    * @return str - $price
    *
    **/
	public static function calculate_booking_price( $price, $data, $price_type, $product, $_product ) {

        if ( true === wceb_is_bookable( $_product ) && apply_filters( 'easy_booking_calculate_booking_price', true, $_product ) ) {
                
            $number_of_dates = wceb_get_product_number_of_dates_to_select( $_product );
            $dates = $number_of_dates === 'one' ? 'one_date' : 'two_dates';

            $price = apply_filters(
                'easy_booking_' . $dates . '_price',
                $price * $data['duration'],
                $product, $_product, $data, $price_type
            );

        }

	    return apply_filters( 'easy_booking_new_' . $price_type, wc_format_decimal( $price ), $data, $product, $_product );

	}

	/**
    *
    * Get booking price details.
    * @param WC_Product - $product
    * @param array - $booking_data
    * @param str - $price_type
    * @return str - $details
    *
    **/
	public static function get_booking_price_details( $product, $booking_data, $new_price ) {

		$details = '';

        if ( wceb_get_product_booking_dates( $product ) === 'two' ) {

            $duration = $booking_data['duration'];
            $average_price = floatval( $new_price / $duration );

            $booking_duration = wceb_get_product_booking_duration( $product );
            
            if ( $booking_duration === 'custom' ) {
                $custom_duration = wceb_get_product_custom_booking_duration( $product );
                $duration *= $custom_duration;
            }

            if ( $booking_duration === 'weeks' ) {
                $unit = _n( 'week', 'weeks', $duration, 'woocommerce-easy-booking-system' );
            } else {
                $booking_mode = get_option( 'wceb_booking_mode' ); // Calculation mode (Days or Nights)
                $unit = $booking_mode === 'nights' ? _n( 'night', 'nights', $duration, 'woocommerce-easy-booking-system' ) : _n( 'day', 'days', $duration, 'woocommerce-easy-booking-system' );
            }

            $details .= apply_filters(
                'easy_booking_total_booking_duration_text',
                sprintf(
                    __( 'Total booking duration: %s %s', 'woocommerce-easy-booking-system' ),
                    absint( $duration ),
                    esc_html( $unit )
                ),
                $duration, $unit
            );

            // Maybe display average price (if there are price variations. E.g Duration discounts or custom pricing)
            if ( true === apply_filters( 'easy_booking_display_average_price', false, $product->get_id() ) ) {
                $details .= '<br />';
                $details .= apply_filters(
                    'easy_booking_average_price_text',
                    sprintf(
                        __( 'Average price %s: %s', 'woocommerce-easy-booking-system' ),
                        wceb_get_price_html( $product ),
                        wc_price( $average_price )
                    ),
                    $product, $average_price
                );
            }
            
        }

        return apply_filters( 'easy_booking_booking_price_details', $details, $product, $booking_data );

	}

}

endif;