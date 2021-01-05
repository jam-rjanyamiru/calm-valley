<?php

namespace EasyBooking;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyBooking\Product' ) ) :

class Product {

	public function __construct() {
        
        add_filter( 'woocommerce_get_price_html', array( $this, 'bookable_product_price_html' ), 10, 2 );
        
	}

    /**
    *
    * Displays a custom price if the product is bookable on the product page
    *
    * @param str | $price
    * @param WC_Product | $product
    * @return str | $price
    *
    **/
    public function bookable_product_price_html( $price, $product ) {

        if ( wceb_is_bookable( $product ) ) {

            // Get price suffix (/ day, / week, etc.)
            $suffix = wceb_get_product_price_suffix( $product );

            if ( empty( $suffix ) || $price === '' ) {
                return $price;
            }

            $price_html = $price . '<span class="wceb-price-format">' . esc_html( $suffix ) . '</span>';

            // Backward compatibility
            $price_html = apply_filters_deprecated( 'easy_booking_display_price', array( $price_html, $product, $price ), '2.2.7', 'easy_booking_price_html' );
            
            return apply_filters(
                'easy_booking_price_html',
                $price_html,
                $product,
                $price
            );

        }
        
        return $price;

    }
    
}

return new Product();

endif;