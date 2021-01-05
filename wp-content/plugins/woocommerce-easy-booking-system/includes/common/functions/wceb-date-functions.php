<?php

defined( 'ABSPATH' ) || exit;

function wceb_is_valid_date( $date ) {

	if ( ! preg_match( '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/', $date ) ) {
        return false;
    }

    return true;

}