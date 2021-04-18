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
        add_action('woocommerce_api_check_camping_dinner_available', [$this, 'check_camping_dinner_available'], 10);
        add_filter('pre_get_posts', [$this, 'exclude_other_post_types_from_search'] );
        add_shortcode( 'search_camping_cart_form', [$this, 'search_camping_cart_book_record_form'] );
        add_action( 'admin_menu', [$this, 'add_menu_page']);
        add_action( 'woocommerce_thankyou', [$this, 'add_camping_cart_booking_info']);
        add_action( 'template_redirect', [$this, 'redirect_another_page']);
    }

    public function load_frontend_files()
    {
        wp_register_style('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/css/frontend.css');
        wp_enqueue_style('frontend-from-ocean-child');

        wp_register_script('frontend-from-ocean-child', get_stylesheet_directory_uri() . '/assets/js/frontend.js');
        wp_enqueue_script('frontend-from-ocean-child');
    }

    public function show_export_orders(){
        $all_order_ids = get_posts(array(
            'post_status' => array('wc-pending',
                'wc-on-hold',
                'wc-processing',
                'wc-completed',
                'wc-cancelled',
                'wc-refunded',
                'wc-failed',
            ),
            'post_type' => 'shop_order' ,
            'numberposts' => '-1',
            'posts_per_page'  => -1,
            'fields' => 'ids',
            'order' => 'asc'
        ));
        $selected_id = '';
        $order_info = array();
        $order_user_info =  array();
        $order_buyer_info = array();
        $order_meal_info = array();
        $order_item_info = array();
        if(isset($_POST['order_id'])){
            $selected_id = $_POST['order_id'];
            $order = wc_get_order($selected_id);
            foreach ($order->get_items() as $item_id => $item) {
                $order_id = $order->ID;
//                    這裡取得的是父商品ID

                $order_item_info['pd_id'] = $item->get_product_id();
                $order_pd = wc_get_product($order_item_info['pd_id']);

                $order_item_info['pd_name'] = $order_pd->get_name();

                $order_buyer_info['first_name'] = $order->get_billing_first_name();
                $order_buyer_info['last_name'] = $order->get_billing_last_name();
                $order_buyer_info['billing_id_card'] = get_post_meta($order_id, 'billing_id_card', true);
                $order_buyer_info['billing_gender'] = get_post_meta($order_id, 'billing_gender', true);
                $order_buyer_info['billing_tel'] = get_post_meta($order_id, 'billing_tel', true);
                $order_buyer_info['billing_phone'] = get_post_meta($order_id, 'billing_phone', true);
                $order_buyer_info['billing_address_1'] = get_post_meta($order_id, 'billing_address_1', true);
                $order_buyer_info['billing_country'] = get_post_meta($order_id, 'billing_country', true);
                $order_buyer_info['billing_birth'] = get_post_meta($order_id, 'billing_birth', true);

                $order_user_info['first_name'] = get_post_meta($order_id, 'user_first_name', true);
                $order_user_info['last_name'] = get_post_meta($order_id, 'user_last_name', true);
                $order_user_info['user_id_card'] = get_post_meta($order_id, 'user_id_card', true);
                $order_user_info['user_gender'] = get_post_meta($order_id, 'user_gender', true);
                $order_user_info['user_phone'] = get_post_meta($order_id, 'user_phone', true);
                $order_user_info['order_comments'] = get_post_meta($order_id, 'order_comments', true);
                $order_user_info['order_country'] = get_post_meta($order_id, 'order_country', true);
                $order_user_info['user_birth'] = get_post_meta($order_id, 'user_birth', true);
                $order_user_info['user_address_1'] = get_post_meta($order_id, 'user_address_1', true);

                $order_info['booking_start_date'] = $item->get_meta('_booking_start_date', true);
                $order_info['booking_end_date'] = $item->get_meta('_booking_end_date', true);
                $order_info['max_people'] = get_term_by('slug',$item->get_meta( 'pa_max_people', true ), 'pa_max_people')->name;
                $order_info['user_invoice_number'] = get_post_meta($order_id, 'user_invoice_number', 1);
                $order_info['user_action_butler'] = get_post_meta($order_id, 'user_action_butler', 1);
                $order_info['user_add_fourth_meal'] = get_post_meta($order_id, 'user_add_fourth_meal', 1);
                $order_info['user_is_vegetarian'] = get_post_meta($order_id, 'user_is_vegetarian', 1);
                $order_info['user_is_driving'] = get_post_meta($order_id, 'user_is_driving', 1);
//                    因為一個購物車限定一個商品，而當入住期間有不同價格，便會造成有兩個商品，
//                    ，然後上述取得的資訊不會因為只讀一個商品而導致錯誤，還有預期是一列為一筆訂單，
//                    因此這裡直接break，以防出問題。
                break;
            }


            $order_booking_info = unserialize(get_post_meta($selected_id, '_order_booking_info', 1));
            foreach($order_booking_info as $info){
                $order_meal_info['dinner_date'] = $info['booking_date'];
                $order_meal_info['dinner'] = $info['dinner'];
                $order_meal_info['eat_beef'] = $info['eat_beef'];
                $order_meal_info['time_period'] = $info['time_period'];
            }
        }


        ?>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="<?=get_stylesheet_directory_uri()?>/assets/js/export_order.js"></script>
        <div class="export_orders_content" id="export_result">
            <h3>匯出訂單</h3>
            <form method="post">
                <select name="order_id">
                    <?php
                        foreach($all_order_ids as $id){
                            ?><option value="<?=$id?>"  <?=$selected_id==$id?'selected':''?>>訂單編號<?=$id?></option><?php
                        }
                    ?>
                </select>
                <input type="submit">
            </form>
            <div id="export-result">
                <?php
                if(isset($_POST['order_id'])) {
                    if(!empty($order_info)){
                        echo '<div style="font-weight:bold;">訂單總資訊</div>';
                        ?>
                        <div>預約起始日:<?=$order_info['booking_start_date']?></div>
                        <div>預約結束日:<?=$order_info['booking_end_date']?></div>
                        <div>預約最大人數:<?=$order_info['max_people']?></div>
                        <div>統一編號:<?=$order_info['user_invoice_number']?></div>
                        <div>行動管家:<?=$order_info['user_action_butler']?></div>
                        <div>是否增加第四餐:<?=$order_info['user_add_fourth_meal']?></div>
                        <div>是否吃素:<?=$order_info['user_is_vegetarian']?></div>
                        <div>是否開車:<?=$order_info['user_is_driving']?></div>
                        <hr>
                        <?php
                    }


                    if(!empty($order_user_info)){
                        echo '<div style="font-weight:bold;">訂單使用者資訊</div>';
                        ?>
                        <div>使用者名字:<?=$order_user_info['first_name']?></div>
                        <div>使用者姓氏:<?=$order_user_info['last_name']?></div>
                        <div>使用者身分證字號:<?=$order_user_info['user_id_card']?></div>
                        <div>使用者性別:<?=$order_user_info['user_gender']?></div>
                        <div>使用者手機:<?=$order_user_info['user_phone']?></div>
                        <div>訂單備註:<?=$order_user_info['order_comments']?></div>
                        <div>訂單備註:<?=$order_user_info['order_comments']?></div>
                        <div>訂單國家:<?=$order_user_info['order_country']?></div>
                        <div>使用者生日:<?=$order_user_info['user_birth']?></div>
                        <div>使用者地址:<?=$order_user_info['user_address_1']?></div>
                        <hr>
                        <?php
                    }


                    if(!empty($order_buyer_info)){
                        echo '<div style="font-weight:bold;">訂單訂購者資訊</div>';
                        ?>
                        <div>訂購者名字:<?=$order_buyer_info['first_name']?></div>
                        <div>訂購者姓氏:<?=$order_buyer_info['last_name']?></div>
                        <div>訂購者身分證字號:<?=$order_buyer_info['billing_id_card']?></div>
                        <div>訂購者性別:<?=$order_buyer_info['billing_gender']?></div>
                        <div>訂購者室內電話:<?=$order_buyer_info['billing_tel']?></div>
                        <div>訂購者手機:<?=$order_buyer_info['billing_phone']?></div>
                        <div>訂購者地址:<?=$order_buyer_info['billing_address_1']?></div>
                        <div>訂購者國家:<?=$order_buyer_info['billing_country']?></div>
                        <div>訂購者生日:<?=$order_buyer_info['billing_birth']?></div>
                        <hr>
                        <?php
                    }


                    if(!empty($order_meal_info)){
                        $trans_meal = ['roast' => '燒烤', 'steam' => '蒸煮海鮮'];
                        $trans_eat_beef = ['y' => '是', 'n' => '否'];
                        $trans_meal_time = ['any' => '不限時段', 'five_thirty' => '下午 5點30~7點', 'seven' => '下午 7點~8點30'];

                        echo '<div style="font-weight:bold;">訂單用餐資訊</div>';
                        ?>
                        <div>用餐日期:<?=$order_meal_info['dinner_date']?></div>
                        <div>晚餐餐點:<?=$trans_meal[$order_meal_info['dinner']]?></div>
                        <div>是否吃牛:<?=$trans_eat_beef[$order_meal_info['eat_beef']]?></div>
                        <div>時段:<?=$trans_meal_time[$order_meal_info['time_period']]?></div>
                        <hr>
                        <?php
                    }


                    if(!empty($order_item_info)){
                        echo '<div style="font-weight:bold;">訂單商品資訊</div>';
                        ?>
                        <div>露營車ID:<?=$order_item_info['pd_id']?></div>
                        <div>露營車名稱:<?=$order_item_info['pd_name']?></div>
                        <hr>
                        <?php
                    }
                    echo '<h4>使用者請簽名:  ˍˍˍˍˍˍˍˍˍˍˍˍ  (Check in用)</h4>';
                }else{
                    echo '無';
                }
                ?>
            </div>
        </div>
        <?php
        if(isset($_POST['order_id'])){
            ?>
            <script>
                var element = document.getElementById('export-result');
                var opt = {
                    margin:       1,
                    filename:     'CheckIn_<?=$_POST['order_id']?>.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2 },
                    jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf(element, opt);
            </script>
            <?php
        }
    }

    public function redirect_another_page(){
        if(
            is_shop()
            ||  is_cart()
            ||  is_product()
            ||  is_product_category()
            ||  is_product_tag()
            ||  is_product_taxonomy()
        ){
            wp_redirect(home_url() . '/booking');
            exit();
        }
    }

    public function check_camping_dinner_available(){

        if(!isset($_POST['booking_date']))
            return 0;


        $booking_date = $_POST['booking_date'];
        $valid_orders = wc_get_orders(['post_status' => [
                'wc-pending',
                'wc-processing',
                'wc-on-hold',
                'wc-completed',
            ],
            'posts_per_page'  => -1,
            'date_before' => date("Y-m-d", strtotime($booking_date)),
            'date_after' => date('Y-m-d', strtotime($booking_date . '-60 days')),
        ]);

        $all_available_time = ['five_thirty', 'seven'];
        $five_thirty_count_booking = 0;
        $seven_count_booking = 0;
        $max_booking_amount = 6;
        foreach($valid_orders as $order){
            if( $order_booking_info = unserialize(get_post_meta($order->get_id(), '_order_booking_info', 1)) ){
                foreach($order_booking_info as $single_day_info){
                    if( str_replace('-', '/' ,$booking_date) == str_replace('-', '/', $single_day_info['booking_date']) ){
                        if( $single_day_info['time_period'] == 'any' ){
                            $five_thirty_count_booking += 1;
                            $seven_count_booking += 1;
                        }else if( $single_day_info['time_period'] == 'seven' ){
                            $seven_count_booking += 1;
                        }else if( $single_day_info['time_period'] == 'five_thirty'){
                            $five_thirty_count_booking += 1;
                        }

                        if( $five_thirty_count_booking == $max_booking_amount){
                            if (($key = array_search('five_thirty', $all_available_time)) !== false) {
                                unset($all_available_time[$key]);
                            }
                        }


                        if( $seven_count_booking == $max_booking_amount){
                            if (($key = array_search('seven', $all_available_time)) !== false) {
                                unset($all_available_time[$key]);
                            }
                        }

                        if( empty($all_available_time) ){
                            break;
                        }
                    }
                }
            }

            if( empty($all_available_time) ){
                break;
            }
        }

        $all_available_time = array_values(array_filter($all_available_time));
        echo json_encode($all_available_time);
        exit;
    }

    public function add_camping_cart_booking_info($order_id ){
        if ( ! $order_id )
            return;

        if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
            $order = wc_get_order($order_id);
            $order->add_order_note($_SESSION['custom_order_note']);
            $order->update_meta_data('_thankyou_action_done', true);
            $order->update_meta_data('_order_booking_info',  sanitize_text_field(serialize($_SESSION['order_booking_info'])));
            $order->save();
        }
    }

    public function show_setting_holiday(){
        if($_POST['from'] == 'setting_holiday'){
            $post_date = sanitize_text_field($_POST['date']);

            if( isset($post_date) ) {
                update_option('custom_setting_holiday_date', $post_date);
            }
        }
        ?>
            <link rel="stylesheet" href="<?=get_stylesheet_directory_uri()?>/assets/css/air-datepicker.min.css">
            <script src="<?=get_stylesheet_directory_uri()?>/assets/js/air-datepicker.min.js"></script>
            <script src="<?=get_stylesheet_directory_uri()?>/assets/js/datepicker.zh.js"></script>
            <script src="<?=get_stylesheet_directory_uri()?>/assets/js/holiday_date.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <h3>設定假日日期</h3>
            <div>
                <?php
                    if( get_option('custom_setting_holiday_date') !='' ){
                        $explode_date = explode(',', get_option('custom_setting_holiday_date'));
                        print('假日:<br>');
                        foreach($explode_date as $date){
                            print($date);
                            print('<br>');
                        }
                    }
                ?>
            </div>
            <form method="post">
                <input type="hidden" name="from" value="setting_holiday" >
                <label for="setting_date">假日日期</label>
                <input type="datepicker" id="setting_date" name="date" value="<?=(get_option('custom_setting_holiday_date') != '')?get_option('custom_setting_holiday_date'):''?>">
                <div>請參考範例格式: 如果是西元2020年2月2號的話，請輸入「2020-02-02」</div>
                <br>
                <input type="submit" value="送出">
            </form>
        <?php
    }

    public function add_menu_page(){

        add_menu_page(
            '設定假日時間',
            '設定假日時間',
            'manage_options',
            'setting_holiday_date',
            array($this, 'show_setting_holiday'),
            '',
            2
        );

        add_menu_page(
            '匯出訂單',
            '匯出訂單',
            'manage_options',
            'calm_valley_export_orders',
            array($this, 'show_export_orders'),
            '',
            3
        );

    }

    public function filter_camping_cart_book_record(){

        if(!isset($_POST['from']) || !isset($_POST['phone']) || !isset($_POST['birth'])){
            return false;
        }

        //只開放查詢60天內預約的紀錄
        $start_date = new DateTime('now');
        $start_date = $start_date->format('Y/m/d');
        $phone = $_POST['phone'];
        $birth = $_POST['birth'];
        $post_orders = array();
        if($_POST['from'] == 'filter_camping_cart_book_record' && is_numeric($phone) && ($birth)) {
            $post_orders = get_posts(array(
                'date_before' => date("Y-m-d",  strtotime($start_date)),
                'date_after' => date('Y-m-d', strtotime($start_date . '-60 days')),
                'post_status' => array('wc-on-hold', 'wc-processing','wc-completed'),
                'post_type' => 'shop_order' ,
                'numberposts' => '-1',
                'posts_per_page'  => -1,
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
            $yes_no_chinese = array('y' => '是', 'n' => '否');
            foreach($post_orders as $index => $post_order) {
                $choose_meal_name = '<br>';
                $order = wc_get_order($post_order->ID);
                if(isset($yes_no_chinese[get_post_meta($post_order->ID, 'user_is_driving', 1)])){
                    $is_driving_name = $yes_no_chinese[get_post_meta($post_order->ID, 'user_is_driving', 1)];
                }else{
                    $is_driving_name = '未知';
                }
                $tmp_order_booking_info = unserialize(get_post_meta($post_order->ID, '_order_booking_info', 1));
                foreach($tmp_order_booking_info as $info){
                    $choose_meal_name .= '入住日期:'.$info['booking_date'].'<br>';
                    $choose_meal_name .= '晚餐:'.$info['dinner'].'<br>';
                    $choose_meal_name .= '是否吃牛:'.$info['eat_beef'].'<br>';
                    $choose_meal_name .= '時段:'.$info['time_period'].'<br><br>';
                }

                foreach ($order->get_items() as $item_id => $item) {
                    $order_id = $order->ID;
//                    這裡取得的是父商品ID
                    $order_pd_id = $item->get_product_id();
                    $order_pd = wc_get_product($order_pd_id);
                    $order_pd_name = $order_pd->get_name();
                    $order_billing_first_name = $order->get_billing_first_name();
                    $order_billing_last_name = $order->get_billing_last_name();
                    $order_user_first_name = get_post_meta($order_id, 'user_first_name', true);
                    $order_user_last_name = get_post_meta($order_id, 'user_last_name', true);
                    $order_booking_start_date = $item->get_meta('_booking_start_date', true);
                    $order_booking_end_date = $item->get_meta('_booking_end_date', true);

                    $order_item_variation_max_people = $item->get_meta( 'pa_max_people', true );
                    $max_people_name = get_term_by('slug', $order_item_variation_max_people, 'pa_max_people')->name;
//                    因為一個購物車限定一個商品，而當入住期間有不同價格，便會造成有兩個商品，
//                    ，然後上述取得的資訊不會因為只讀一個商品而導致錯誤，還有預期是一列為一筆訂單，
//                    因此這裡直接break，以防出問題。
                    break;
                }
                $order_total = (int)$order->get_total();
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
        if ( !is_admin() && $query->is_main_query() && is_search() ) {
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

        $count_holidays = 0;
        $count_booking_days = 0;
        $count_weekdays = 0;

        $weekday_variation_id = 1;
        $holiday_variation_id = 1;

        $weekday_price = 1;
        $holiday_price = 1;

        $timestamp_booking_start_date = strtotime($_SESSION['booking_start_date']);
        $timestamp_booking_end_date = strtotime($_SESSION['booking_end_date']);
        $count_booking_days = ($timestamp_booking_end_date - $timestamp_booking_start_date)/(60*60*24);

        if( get_option('custom_setting_holiday_date') != '' ){
            $explode_date = explode(',', get_option('custom_setting_holiday_date'));
            //                    Custom Holidays
            $tmp_timestamp_booking_end_date = strtotime("-1 day", $timestamp_booking_end_date);

            foreach($explode_date as $date){
                $timestamp_holiday_date = strtotime($date);
                if($timestamp_holiday_date == $timestamp_booking_start_date
                    || (
                        $timestamp_holiday_date > $timestamp_booking_start_date
                        && $timestamp_holiday_date <= $tmp_timestamp_booking_end_date)
                ){
                    $count_holidays += 1;
                    continue;
                }
            }
        }

        //                    General Holidays
        $timestamp = $timestamp_booking_start_date;
        $skipdays = array("Friday", "Saturday");
        while ($timestamp < $timestamp_booking_end_date) {
            if ( (in_array(date("l", $timestamp), $skipdays)) )
            {
                $count_holidays += 1;
            }
            $timestamp = strtotime("+1 day", $timestamp);
        }
        $count_weekdays = $count_booking_days - $count_holidays;


        foreach(json_decode($cart_data) as $key => $single_data){
//            目前這個迴圈都只會跑一次，因為一台購物車只會接收一次顧客要求的商品，但實際上有可能有兩個商品，因為規格問題
            $product_id = $single_data->pd_id;
            $quantity = 1;
            $max_people = $single_data->max_people;
            $product_cart_id = WC()->cart->generate_cart_id( $product_id );

            $product = wc_get_product($product_id);
            foreach ($product->get_available_variations() as $variation) {
//                確認此商品的可變類型是否有等於顧客想要的 => 再取出平日和假日的原價和促銷價
                $variation_id = $variation['variation_id'];
                $variation_product = wc_get_product($variation_id);

                if ($variation['attributes']['attribute_pa_max_people'] == $max_people) {
                    if ($variation['attributes']['attribute_pa_is_weekday_or_is_holiday'] == 'weekday') {
                        if ($variation_product->get_sale_price() != '') {
                            $weekday_price = $variation_product->get_sale_price();
                        }else{
                            $weekday_price = $variation_product->get_regular_price();
                        }
                        $weekday_variation_id = $variation_id;
                    }
                    else if ($variation['attributes']['attribute_pa_is_weekday_or_is_holiday'] == 'holiday') {
                        if ($variation_product->get_sale_price() != '') {
                            $holiday_price = $variation_product->get_sale_price();
                        }else{
                            $holiday_price = $variation_product->get_regular_price();
                        }
                        $holiday_variation_id = $variation_id;
                    }

                    if ($weekday_variation_id != 1 && $holiday_variation_id != 1){
                        break;
                    }
                }
            }

            if( ! WC()->cart->find_product_in_cart( $product_cart_id ) ){
                if ($count_weekdays > 0) {
                    $total_weekday_price = ($weekday_price * $count_weekdays);
                    $weekday_booking_pd_meta_data = array(
                        '_booking_price' => $total_weekday_price,
                        '_booking_start_date' => $_SESSION['booking_start_date'],
                        '_booking_end_date' => $_SESSION['booking_end_date'],
                        '_booking_duration' => $_SESSION['booking_days'],
                        '_ebs_start' => $_SESSION['booking_start_date'],
                        '_ebs_end' => $_SESSION['booking_end_date'],
                    );
                    $weekday_variation_data = array('attribute_pa_max_people' => $max_people);
                    WC()->cart->add_to_cart( $product_id, $quantity, $weekday_variation_id ,$weekday_variation_data, $weekday_booking_pd_meta_data);
                }

                if ($count_holidays > 0) {
                    $total_holiday_price = ($holiday_price * $count_holidays);
                    $holiday_booking_pd_meta_data = array(
                        '_booking_price' => $total_holiday_price,
                        '_booking_start_date' => $_SESSION['booking_start_date'],
                        '_booking_end_date' => $_SESSION['booking_end_date'],
                        '_booking_duration' => $_SESSION['booking_days'],
                        '_ebs_start' => $_SESSION['booking_start_date'],
                        '_ebs_end' => $_SESSION['booking_end_date'],
                    );
                    $holiday_variation_data = array('attribute_pa_max_people' => $max_people);
                    WC()->cart->add_to_cart( $product_id, $quantity, $holiday_variation_id ,$holiday_variation_data, $holiday_booking_pd_meta_data);
                }
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
                $_SESSION['booking_start_date'] = $_POST['start_date'];
                $_SESSION['booking_end_date'] = $_POST['end_date'];
                $_SESSION['booking_days'] = $_POST['days'];
                $_SESSION['booking_step'] = '2';
                include_once (get_stylesheet_directory().'/includes/booking_camping_step_two.php');
                break;
            case 'three':
                $_SESSION['booking_step'] = '3';
                $tmp_order_note = '';
                $trans_meal = ['roast' => '燒烤', 'steam' => '蒸煮海鮮'];
                $trans_eat_beef = ['y' => '是', 'n' => '否'];
                $trans_meal_time = ['any' => '不限時段', 'five_thirty' => '下午 5點30~7點', 'seven' => '下午 7點~8點30'];
                $tmp_date = $_SESSION['booking_start_date'];
                $order_booking_info_arr = [];
                for($i=0;$i<$_SESSION['booking_days'];$i++){
                    $tmp_order_note .= '
                         入住日期: '.$tmp_date.' ,
                        晚餐: '.$trans_meal[$_POST['meal_'.$i]].' ,
                        是否吃牛: '.$trans_eat_beef[$_POST['eat_beef_'.$i]].' ,
                        時段: '.$trans_meal_time[$_POST['meal_time_'.$i]];

                    array_push($order_booking_info_arr, [
                            'booking_date' => $tmp_date,
                            'dinner' => $_POST['meal_'.$i],
                            'eat_beef' => $_POST['eat_beef_'.$i],
                            'time_period' => $_POST['meal_time_'.$i],
                        ]);

                    $tmp_date = date('Y/m/d', strtotime($tmp_date . "+1 days" ));
                }
                $_SESSION['order_booking_info'] = $order_booking_info_arr;
                $_SESSION['custom_order_note'] = $tmp_order_note;

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
            'date_after' => date('Y-m-d', strtotime($start_date . '+ ' . $days . '-60 days')),
            'posts_per_page'  => -1,
        ]);
        $exclude = array();
        foreach($orders as $order){
            foreach ( $order->get_items() as $item_id => $item ) {
                $order_booking_start_date = strtotime($item->get_meta( '_booking_start_date', true ));
                $order_booking_end_date = strtotime($item->get_meta( '_booking_end_date', true ));

//                If order date in customer date, it will be exclude.
                $is_cover = false;
                if (
                    is_numeric($order_booking_start_date)
                    && is_numeric($order_booking_end_date)
                ) {
                    if ($order_booking_end_date > $customer_end_date_timestamp) {
                        $right_start_date = $order_booking_start_date;
                        $left_end_date = $customer_end_date_timestamp;
                        if ($right_start_date < $left_end_date) {
                            $is_cover = true;
                        }
                    }else {
                        $right_start_date = $customer_booking_date_timestamp;
                        $left_end_date = $order_booking_end_date;
                        if ($right_start_date < $left_end_date) {
                            $is_cover = true;
                        }
                    }
                }

                if ($is_cover) {
                    array_push($exclude, $item->get_product_id());
                }
            }
        }

        $exclude = array_unique($exclude);

        $pds = wc_get_products([
            'stock_status' => 'instock',
            'exclude' => $exclude,
            'posts_per_page'  => -1,
        ]);

        $camping_location = array();
        if(isset($_POST['is_taking_pet']) && $_POST['is_taking_pet'] == '1'){
            $is_taking_pet = 1;
        }else{
            $is_taking_pet = 0;
        }

        foreach($pds as $pd){
            $tmp_location_x = get_post_meta($pd->get_id(), 'camping_location_x', 1);
            $tmp_location_y = get_post_meta($pd->get_id(), 'camping_location_y', 1);
            $bookable = get_post_meta($pd->get_id(), '_bookable', 1);
            $tmp_is_taking_pet = get_post_meta($pd->get_id(), 'is_taking_pet', 1);
            if($tmp_is_taking_pet != 1){
                $tmp_is_taking_pet = 0;
            }else{
                $tmp_is_taking_pet = 1;
            }

            if($bookable == 'yes' && $tmp_location_x != '' && $tmp_location_y != '' && $tmp_is_taking_pet == $is_taking_pet){
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
