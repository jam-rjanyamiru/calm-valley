<?php
/**
 * Child theme functions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: oceanwp
 * @link http://codex.wordpress.org/Plugin_API
 *
 */
require_once __DIR__ . '/includes/trait_instance.php';

Class CalmValley
{
    use Plugin_Instance;

    public function __construct()
    {
        $this->register_hooks();
//        $this->include_files();
    }

    private function register_hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'load_frontend_files'], 101);
        add_action('woocommerce_api_available_camper', [$this, 'available_camper'], 10);
        add_action('woocommerce_api_change_camping_content', [$this, 'change_camping_content'], 10);
        add_filter( 'woocommerce_add_to_cart_validation', [$this, 'woocommerce_custom_before_add_to_cart'] );
        add_action( 'wp_ajax_nopriv_woocommerce_add_variation_to_cart', [$this, 'woocommerce_custom_add_variation_to_cart'] );
    }

//    private function include_files()
//    {
//    }

//    public function header_script_for_country_input()
//    {
//        if (is_page('cart')) {
//            wp_register_style('chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css');
//            wp_enqueue_style('chosen');
//
//            wp_register_script('chosen', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', null, null, true);
//            wp_enqueue_script('chosen');
//
//<!--            <script type='text/javascript'>-->
//<!--                jQuery(function ($) {-->
//<!--                    $('#cart_country_select').chosen();-->
//<!--                });-->
//<!--            </script>-->
//<!--            -->
//        }
//    }

    public function load_frontend_files()
    {

        wp_register_style('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/css/frontend.css');
        wp_enqueue_style('frontend-from-ocean-child');

        wp_register_script('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/js/frontend.js');
        wp_enqueue_script('frontend-from-ocean-child');

        wp_register_script('wc-variation-add-to-cart', get_stylesheet_directory_uri() . '/assets/js/booking.js');
        wp_enqueue_script('wc-variation-add-to-cart');
        $vars = array( 'ajax_url' => admin_url( 'admin-ajax.php' ) );
        wp_localize_script( 'wc-variation-add-to-cart', 'WC_VARIATION_ADD_TO_CART', $vars );
    }

    public function woocommerce_custom_add_variation_to_cart() {

        ob_start();

        $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );

        $variation_id      = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : '';
        $variations         = ! empty( $_POST['variation'] ) ? (array) $_POST['variation'] : '';

        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations, $cart_item_data );

        if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {

            do_action( 'woocommerce_ajax_added_to_cart', $product_id );

            if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
                wc_add_to_cart_message( $product_id );
            }

            // Return fragments
            WC_AJAX::get_refreshed_fragments();

        } else {

            // If there was an error adding to the cart, redirect to the product page to show any errors
            $data = array(
                'error' => true,
                'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
            );

            wp_send_json( $data );

        }

        die();
    }

    public function woocommerce_custom_before_add_to_cart( $cart_item_data ) {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
        return true;
    }

    public function change_camping_content()
    {
        if(!isset($_POST['to']))
        {
            return false;
        }

        switch($_POST['to'])
        {
            case 'step':
                break;
            default:
                break;
        }
    }

    public function available_camper()
    {
        if(!isset($_POST['start_date']) || !isset($_POST['days']))
        {
            return false;
        }


        $start_date = $_POST['start_date'];
        $days = $_POST['days'];
        $customer_booking_date_timestamp = strtotime($start_date);
        $customer_end_date_timestamp = strtotime($start_date . '+ ' . $days . ' days');

//      目前只開放60天前預約
        $orders = wc_get_orders([
            'date_before' => date("Y-m-d", strtotime($start_date . '+ ' . $days . ' days')),
            'date_after' => date('Y-m-d', strtotime($start_date . '+ ' . $days . '-60 days'))
        ]);
        $exclude = array();
        foreach($orders as $order){
            foreach ( $order->get_items() as $item_id => $item ) {
                $order_booking_start_date = strtotime($item->get_meta( '_booking_start_date', true ));
                $order_booking_end_date = strtotime($item->get_meta( '_booking_end_date', true ));

                if(
                    is_numeric($order_booking_start_date)
                    && is_numeric($order_booking_end_date)
                    && (
                            (
                                $order_booking_start_date >= $customer_booking_date_timestamp
                                &&
                                $order_booking_end_date <= $customer_end_date_timestamp
                            )
                        ||  (
                                $order_booking_start_date <= $customer_booking_date_timestamp
                                &&
                                $order_booking_end_date >= $customer_end_date_timestamp
                            )
                        ||
                            (
                                $order_booking_start_date <= $customer_booking_date_timestamp
                                &&
                                $order_booking_end_date >= $customer_booking_date_timestamp
                            )
                        ||
                            (
                                $order_booking_start_date <= $customer_end_date_timestamp
                                &&
                                $order_booking_end_date >= $customer_end_date_timestamp
                            )
                    )
                ) {
                    array_push($exclude, $item->get_product_id());
                }
            }
        }

        $exclude = array_unique($exclude);

        $pds = wc_get_products([
            'stock_status' => 'instock',
            'exclude' => $exclude
        ]);

        $camping_location = array();
        foreach($pds as $pd){
            $tmp_location_x = get_post_meta($pd->get_id(), 'camping_location_x', 1);
            $tmp_location_y = get_post_meta($pd->get_id(), 'camping_location_y', 1);
            $bookable = get_post_meta($pd->get_id(), '_bookable', 1);
            if($bookable == 'yes' && $tmp_location_x != '' && $tmp_location_y != ''){
                $camping_location[$pd->get_id()] = [intval($tmp_location_x), intval($tmp_location_y)];
            }
        }
        echo json_encode($camping_location);
        exit;
    }
}

$GLOBALS['CalmValley'] = CalmValley::get_instance();



/******** Default Theme ********/
/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */
function oceanwp_child_enqueue_parent_style() {
    // Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
    $theme   = wp_get_theme( 'OceanWP' );
    $version = $theme->get( 'Version' );
    // Load the stylesheet
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );

}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );
