<?php

/**
*
* Action hooks and filters for Polylang.
* @version 2.2.8
*
**/

defined( 'ABSPATH' ) || exit;

/**
*
* Make sure home URL is in the right language for Ajax request, otherwise it causes 403 error.
*
**/
add_filter( 'easy_booking_home_url', 'wceb_pll_home_url', 10, 1);

function wceb_pll_home_url( $home_url ) {
	return function_exists( 'pll_home_url' ) ? pll_home_url() : $home_url;
}

/**
*
* Make sure to tell Polylang it's ajax when selecting dates on frontend so it gets the right language.
*
**/
add_action( 'easy_booking_after_init', 'wceb_pll_ajax_on_front' );

function wceb_pll_ajax_on_front() {

	if ( isset( $_GET['wceb-ajax'] ) && 'set_booking_session' === $_GET['wceb-ajax'] ) {
        add_filter( 'pll_is_ajax_on_front', '__return_true' );
    }

}