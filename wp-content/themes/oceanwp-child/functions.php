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
        add_action('woocommerce_api_filter_camping_cart_book_record', [$this, 'filter_camping_cart_book_record'], 10);
        add_action('wp_head', [$this, 'show_something'], 10);
        add_filter('pre_get_posts', [$this, 'exclude_other_post_types_from_search'] );
        add_shortcode( 'search_camping_cart_form', [$this, 'search_camping_cart_book_record_form'] );
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

    public function filter_camping_cart_book_record(){

        if(!isset($_POST['from']) || !isset($_POST['phone']) || !isset($_POST['birth'])){
            return false;
        }
        
        error_log(print_r('ASDQWE', 1));
        error_log(print_r($_POST, 1));
        
        //只開放查詢60天內預約的紀錄
        $start_date = new DateTime('now');
        $start_date = $start_date->format('Y/m/d');
        $phone = $_POST['phone'];
        $birth = $_POST['birth'];
        $post_orders = array();
        if($_POST['from'] == 'filter_camping_cart_book_record' && is_numeric($phone) && ($birth)) {
            $post_orders = get_posts(array(
                'date_before' => date("Y-m-d", strtotime($start_date)),
                'date_after' => date('Y-m-d', strtotime($start_date . '-60 days')),
                'post_status' => array('wc-on-hold', 'wc-processing','wc-completed'),
                'post_type' => 'shop_order' ,
                'numberposts' => '-1',
                'meta_query' =>
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => '_billing_phone',
                            'value' => $phone,
                            'compare'=> '=='
                        ),
                        array(
                            'key' => '_billing_birth',
                            'value' => $birth,
                            'compare'=> '=='
                        )
                    ),
            ));
        }
        ?>
        <div class="search-result-div">
            <hr>
            <h3>搜尋結果</h3>
            <table class="search-result-table">
                <thead>
                    <tr>
                        <td>訂單編號</td>
                        <td>商品名稱</td>
                        <td>訂購人姓氏</td>
                        <td>訂購人名字</td>
                        <td>使用者姓氏</td>
                        <td>使用者名字</td>
                        <td>開始入住日期</td>
                        <td>結束入住日期</td>
                        <td>供餐選擇</td>
                        <td>是否開車</td>
                        <td>最大人數</td>
                        <td>總價格</td>
                    </tr>
                </thead>
            <?php
    //        這種寫法，每列一筆訂單只適用在一筆訂單只有一個商品時‧
            if( !sizeof($post_orders) ) {
                echo '<tr>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                        <td>無</td>
                      </tr>';
            }
            foreach($post_orders as $index => $post_order) {
                $order = wc_get_order($post_order->ID);
                foreach ($order->get_items() as $item_id => $item) {
                    $order_id = $order->ID;
                    $order_pd_id = $item->get_product_id();
                    $order_pd = wc_get_product($order_pd_id);
                    $order_pd_name = $order_pd->get_name();
                    $order_billing_first_name = $order->get_billing_first_name();
                    $order_billing_last_name = $order->get_billing_last_name();
                    $order_user_first_name = get_post_meta($order_id, 'user_first_name', true);
                    $order_user_last_name = get_post_meta($order_id, 'user_last_name', true);
                    $order_booking_start_date = $item->get_meta('_booking_start_date', true);
                    $order_booking_end_date = $item->get_meta('_booking_end_date', true);

                    $order_pd_variation = $item->get_product();
                    $order_item_variation_choose_meal = $item->get_meta( 'pa_choose_meal', true );
                    $order_item_variation_is_driving = $item->get_meta( 'pa_is_driving', true );
                    $order_item_variation_max_people = $item->get_meta( 'pa_max_people', true );
                    $choose_meal_name = get_term_by('slug', $order_item_variation_choose_meal, 'pa_choose_meal')->name;
                    $is_driving_name = get_term_by('slug', $order_item_variation_is_driving, 'pa_is_driving')->name;
                    $max_people_name = get_term_by('slug', $order_item_variation_max_people, 'pa_max_people')->name;

                    $order_total = $order->get_total();
                    ?>
                    <tr>
                        <td><?=$order_id?></td>
                        <td><?=$order_pd_name?></td>
                        <td><?=$order_billing_first_name?></td>
                        <td><?=$order_billing_last_name?></td>
                        <td><?=$order_user_first_name?></td>
                        <td><?=$order_user_last_name?></td>
                        <td><?=$order_booking_start_date?></td>
                        <td><?=$order_booking_end_date?></td>
                        <td><?=$choose_meal_name?></td>
                        <td><?=$is_driving_name?></td>
                        <td><?=$max_people_name?></td>
                        <td><?=$order_total?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </table>
        </div>
        <?php
        exit;
    }

    public function search_camping_cart_book_record_form()
    {
        include_once (get_stylesheet_directory().'/includes/search_camping_cart_book_record_form.php');
    }

    public function exclude_other_post_types_from_search($query){
        if ( $query->is_main_query() && is_search() ) {
            $query->set( 'post_type', 'post' );
        }
        return $query;
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
