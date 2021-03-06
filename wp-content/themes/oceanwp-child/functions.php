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
    }

    private function register_hooks()
    {
        add_action('wp_enqueue_scripts', [$this, 'load_frontend_files'], 101);
        add_action('woocommerce_api_available_camper', [$this, 'available_camper'], 10);
        add_action('woocommerce_api_add_custom_data_to_cart_step_one', [$this, 'add_custom_data_to_cart_step_one'], 10);
        add_action('woocommerce_api_add_custom_data_to_cart_step_two', [$this, 'add_custom_data_to_cart_step_two'], 10);
        add_action('woocommerce_api_change_camping_content', [$this, 'change_camping_content'], 10);
        add_action('wp_head', [$this, 'show_something'], 10);
    }

    public function load_frontend_files()
    {
        wp_register_style('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/css/frontend.css');
        wp_enqueue_style('frontend-from-ocean-child');

        wp_register_script('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/js/frontend.js');
        wp_enqueue_script('frontend-from-ocean-child');
    }

    public function show_something(){
        if(!session_id()) {
            session_start();
        }
//
//        error_log(print_r('show_something__start', 1));
//        error_log(print_r($_SESSION, 1));
//        error_log(print_r('show_something__ending', 1));
    }

    public function add_custom_data_to_cart_step_two(){
        if(!isset($_POST['result_arr_data'])){
            return false;
        }

        if(!session_id()) {
            session_start();
        }

        global $woocommerce;
        $woocommerce->cart->empty_cart();
        $cart_data = str_replace("\\", "" ,$_POST['result_arr_data']);
        foreach(json_decode($cart_data) as $key => $single_data){
            error_log(print_r('single_data', 1));
            error_log(print_r($single_data->choose_meal, 1));
            $product_id = $single_data->pd_id;
            $quantity = 1;
            $choose_meal = $single_data->choose_meal;
            $is_driving = $single_data->is_driving;
            $max_people = $single_data->max_people;
            $product_cart_id = WC()->cart->generate_cart_id( $product_id );

            $product = wc_get_product($product_id);
            $variation_id = 0;
            $price = 0;
            foreach ($product->get_available_variations() as $variation) {
                $variation_id = $variation['variation_id'];
                $price = $variation['display_price'];
                break;
            }

            if( ! WC()->cart->find_product_in_cart( $product_cart_id ) ){
                $booking_pd_meta_data = array(
                    '_booking_price' => $price,
                    '_booking_start_date' => $_SESSION['booking_start_date'],
                    '_booking_end_date' => $_SESSION['booking_end_date'],
                    '_booking_duration' => $_SESSION['booking_days'],
                    '_ebs_start' => $_SESSION['booking_start_date'],
                    '_ebs_end' => $_SESSION['booking_end_date'],
                );
                $variation = array('attribute_pa_choose_meal' => $choose_meal, 'attribute_pa_is_driving' => $is_driving, 'attribute_pa_max_people' => $max_people);
                WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ,$variation, $booking_pd_meta_data);
                
                error_log(print_r('ADDTOCARTVALUE', 1));
                error_log(print_r($variation, 1));
            }
        }

        exit;
    }

    public function add_custom_data_to_cart_step_one(){
        if(!isset($_POST['product_id'])){
            return false;
        }
        if(!session_id()) {
            session_start();
        }

        $product_ids = $_POST['product_id'];
        $pd_arr = array_unique(explode(',', $product_ids));
        $_SESSION['booking_step'] = '1';
        $_SESSION['booking_pds'] = $pd_arr;
        exit;
    }

    public function change_camping_content()
    {
        if(!isset($_POST['to']))
        {
            return false;
        }

        if(!session_id()) {
            session_start();
        }

        switch($_POST['to'])
        {
            case 'two':
                include_once (get_stylesheet_directory().'/includes/booking_camping_step_two.php');
                $_SESSION['booking_start_date'] = $_POST['start_date'];
                $_SESSION['booking_end_date'] = $_POST['end_date'];
                $_SESSION['booking_days'] = $_POST['days'];
                $_SESSION['booking_step'] = '2';
                break;
            case 'three':
                $_SESSION['booking_step'] = '3';
                include_once (get_stylesheet_directory().'/includes/booking_camping_step_three.php');
                break;
            case 'four':
                $_SESSION['booking_step'] = '4';
                $_SESSION['accept_contract'] = $_POST['accept_contract'];
                echo wc_get_checkout_url();
                break;
            default:
                break;
        }

        exit;
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
