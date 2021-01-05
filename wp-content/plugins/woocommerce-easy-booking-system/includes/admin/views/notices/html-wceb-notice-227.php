<?php

/**
*
* Notice for version 2.2.7.
* Display a notice to inform that a template file has been renamed.
*
*/

defined( 'ABSPATH' ) || exit;

?>

<div class="updated easy-booking-notice">

	<p>

	<?php printf( esc_html__( 'Your theme uses an outdated Easy Booking template. Please rename %s to %s and make sure it is up-to-date.', 'woocommerce_easy_booking_system' ), '/your-theme/easy-booking/<strong>wceb-html-product-view.php</strong>', '/your-theme/easy-booking/<strong>html-wceb-single-product.php</strong>' ); ?>

	</p>

</div>