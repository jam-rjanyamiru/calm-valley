<?php
/*
Plugin Name: WC Order Contact
Description: Add Contact field in member-center page that shows order details.
Version: 1.0.0
Author: RJ
Author URI:
Text Domain: wc-order-contact
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/includes/trait_instance.php';

if( ! class_exists('WC_Order_Contact') ) :

    class WC_Order_Contact
    {

        use Plugin_Instance;

        private static $instance;

        private $setting_parameters = array(
                'plugin_name' => 'wc-order-contact',
                'list_slug' => 'order_contact_center',
                'edit_slug' => 'order_contact_edit',
        );

        public static function get_instance()
        {
            if(is_null(self::$instance)) self::$instance = new self;
            return self::$instance;
        }

        private function __construct()
        {
            load_plugin_textdomain($this->setting_parameters['plugin_name'], false, basename(dirname(__FILE__)) . '/lang');
            $this->register_hooks();
            $this->include_files();
        }

        private function include_files()
        {
            if ( is_admin() || is_page('18') ):

                wp_register_style($this->setting_parameters['plugin_name'].'-css', plugin_dir_url(__FILE__) .'/assets/css/'.$this->setting_parameters['plugin_name'].'.css');
                wp_enqueue_style($this->setting_parameters['plugin_name'].'-css');

                wp_register_script($this->setting_parameters['plugin_name'].'-js', plugin_dir_url(__FILE__) . '/assets/js/'.$this->setting_parameters['plugin_name'].'.js');
                wp_enqueue_script($this->setting_parameters['plugin_name'].'-js');

            endif;
        }

        private function register_hooks()
        {
            add_action( 'woocommerce_view_order', [$this, 'display_in_frontend'], 20 ); //與前台的woocommerce訂單頁面的對話相關
            add_action( 'woocommerce_api_save_order_comments', [$this, 'save_order_comments']); //與前台的woocommerce訂單頁面的對話相關
            add_action( 'admin_menu', [$this, 'add_menu_page']); //後台側邊欄/選單
            add_action( 'woocommerce_api_modify_email_activated', [$this, 'modify_email_activated']); //後台更改是否寄信
            add_action( 'woocommerce_new_order', [$this, 'add_email_activate_meta']); //在創建訂單時，就建立是否要寄信通知的post_meta
            add_action( 'just_send_email', [$this, 'just_send_email_function'], 10, 4); //寄信方式改成排程，避免loading速度太慢。
        }

        public function just_send_email_function($to, $subject, $content, $headers)
        {
            wp_mail($to, $subject, $content, $headers);
//            wp_unschedule_hook( 'just_send_email' ); //如有需要可使用，一寄信後，馬上清掉"全部"。
//            wp_clear_scheduled_hook( 'just_send_email' ); //如有需要可使用，一寄信後，馬上清掉"最近一個"。
        }

        public function add_email_activate_meta($order_id)
        {
            if(!metadata_exists('post', $order_id, 'order_contact_email_activated'))
            {
                $global_setting = get_option('global_order_contact_email_activated');
                if(var_dump($global_setting) === false)
                {
                    $global_setting = 1;
                }
                update_post_meta($order_id, 'order_contact_email_activated', $global_setting);
            }
        }

        public function modify_email_activated()
        {
            if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'modify_email_activated')
            {
                if(isset($_REQUEST['email_activated']))
                {
                    $activated_value = $_REQUEST['email_activated'];
                }else {
                    $activated_value = 0;
                }

                if(isset($_REQUEST['order_id']))
                {
                    //單筆訂單是否要啟用寄信
                    update_post_meta($_REQUEST['order_id'], 'order_contact_email_activated', $activated_value);
                }else
                {
                    //全域是否要啟用信箱寄信
                    update_option('global_order_contact_email_activated', $activated_value);
                }
            }

            if($_REQUEST['from'] == 'edit_page' && isset($_REQUEST['order_id']))
            {
                wp_redirect(admin_url('admin.php?page='.$this->setting_parameters['edit_slug'].'&order_id='.$_REQUEST['order_id']));
            }else{
                wp_redirect(admin_url('admin.php?page='.$this->setting_parameters['list_slug']));
            }
            exit;
        }

        public function display_in_frontend($order_id)
        {
            global $wpdb;
            $order_notes = $this->get_order_notes($order_id);
            ?>
            <div class="order-qa woocommerce">
                <h4><?=__('Comment', $this->setting_parameters['plugin_name'])?></h4>
                <div class="order-qa-area clearfix">
                    <?php
                    foreach($order_notes as $note)
                    {
                        $note_date = $note['note_date'];
                        $note_author = $note['note_author'];
                        $note_content = $note['note_content'];
                        if ( ! $author_id = $wpdb->get_row( $wpdb->prepare(
                            "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $note_author
                        ) ) )
                        {
                            return false;
                        }
                        $author = get_user_by('id', $author_id->ID);

                        $first_name = get_user_meta($author->ID, 'first_name', 1);
                        if($first_name == '')
                        {
                            $author_name = $author->data->user_login;
                        }else{
                            $last_name = get_user_meta($author->ID, 'last_name', 1);
                            $author_name = $last_name.' '.$first_name;
                        }

                        if($author->caps['administrator'] == true)
                        {
                            echo '<div class="qa-a">';
                        }else{
                            echo '<div class="qa-q">';
                        }
                        echo '<p class="name">'.$author_name.'</p>';
                        echo '<p class="time">'.$note_date.' </p>';
                        echo '<p class="message">'.$note_content.'</p>';
                        echo '</div>';
                        echo '<div class="clearfix"></div>';
                    }
                    ?>
                </div>
                <form action="/wc-api/save_order_comments" method="post">
                    <p class="form-row">
                        <textarea name="order_qa_comments" class="input-text" id="order_qa_comments" rows="8" cols="10"></textarea>
                    </p>
                    <input type="hidden" name="from" value="frontend">
                    <input type="hidden" name="order_id" value="<?=$order_id;?>">
                    <p class="clearfix"><input type="submit" class="woocommerce-Button button" name="submit" value="<?=__('Update Comment', $this->setting_parameters['plugin_name'])?>"></p>
                </form>
            </div>
            <?php
        }

        public function save_order_comments()
        {
            $order_qa_comments = $_POST['order_qa_comments'];
            $order_id = $_POST['order_id'];
            $order = wc_get_order( $order_id);
            $subject = '［'.get_option( 'blogname' ).'］ '.__('Notice', $this->setting_parameters['plugin_name']).'！#'.$order_id.' '.__('You have a comment at this order.', $this->setting_parameters['plugin_name']);
            $total_price = 0;
            $content = '';
            foreach ($order->get_items() as $key )
            {
                $product_string = $key['name'];
                $qty = $key['qty'];
                $total_price += $key['qty'] * $key['line_total'];
                $line_total = $key['line_total'];
                $content .= '（'.__('Product Name', $this->setting_parameters['plugin_name']).'）'.$product_string.'<br>'.
                    '（'.__('Product Amount', $this->setting_parameters['plugin_name']).'）'.$qty.'<br>'.
                    '（'.__('Product Unit Price', $this->setting_parameters['plugin_name']).'）'.$line_total.'<br><br>';
            }
            $now = new DateTime('now');
            $now = $now->modify('+8 hours');
            $order_status = $order->get_status();
            $content .= '<hr><br>（'.__('Order ID', $this->setting_parameters['plugin_name']).'）'.$order_id.' <br>'.
                '（'.__('Total Price', $this->setting_parameters['plugin_name']).'）'.$total_price.' <br>'.
                '（'.__('Order Status', $this->setting_parameters['plugin_name']).'）'.$order_status.' <br>'.
                '（'.__('Customer Name', $this->setting_parameters['plugin_name']).'）'.$order->get_formatted_billing_full_name().'<br><br>'.
                '<hr>'.__('Comment Time', $this->setting_parameters['plugin_name']).'：('.$now->format('Y-m-d H:i:s').')<br>'.
                __('Order Comment', $this->setting_parameters['plugin_name']).'：「'.$order_qa_comments.'」';
            $headers = array('Content-Type: text/html; charset=UTF-8');

            if ( $order_qa_comments )
            {
                $comment_id = $order->add_order_note(htmlspecialchars($order_qa_comments), 0, true);
                add_comment_meta( $comment_id, 'is_customer_note', 1);
            }

            $redirect_url = '/';
            if($_REQUEST['from'] == 'frontend')
            {
                $to = get_option('admin_email'); //需要設定管理員的信箱
                $redirect_url = home_url().'/member-center/view-order/'.$order_id;
            }elseif($_REQUEST['from'] == 'backend')
            {
                $to_id = $order->get_customer_id();
                $to = get_user_by('id', $to_id)->data->user_email;
                $redirect_url = admin_url('admin.php?page='.$this->setting_parameters['edit_slug'].'&order_id='.$order_id);
            }

            if(get_post_meta($order_id, 'order_contact_email_activated', 1) == 1)
            {
                sleep(5);
                wp_schedule_single_event( time(), 'just_send_email', array($to, $subject, $content, $headers));
            }
            wp_redirect($redirect_url);
            exit;
        }

        public function add_menu_page()
        {
            /**訂單交談中心**/
            add_menu_page(
                __('Order Contact', $this->setting_parameters['plugin_name']),
                __('Order Contact', $this->setting_parameters['plugin_name']),
                'manage_options',
                $this->setting_parameters['list_slug'], //URL
                array($this, $this->setting_parameters['list_slug']),  //function
                '',
                4
            );

            add_submenu_page(
                $this->setting_parameters['list_slug'], //Parent URL
                __('Order Contact Edit', $this->setting_parameters['plugin_name']),
                __('Order Contact Edit', $this->setting_parameters['plugin_name']),
                'manage_options',
                $this->setting_parameters['edit_slug'],  //URL
                array($this, $this->setting_parameters['edit_slug']) //function
            );
        }

        public function order_contact_center()
        {
//            以下是外部來源
            wp_register_style($this->setting_parameters['plugin_name'].'-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            wp_enqueue_style($this->setting_parameters['plugin_name'].'-bootstrap-css');

            wp_register_style($this->setting_parameters['plugin_name'].'-bootstrap-switch-css', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css');
            wp_enqueue_style($this->setting_parameters['plugin_name'].'-bootstrap-switch-css');

            wp_register_script($this->setting_parameters['plugin_name'].'bootstrap-switch-js', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js');
            wp_enqueue_script($this->setting_parameters['plugin_name'].'bootstrap-switch-js');

            global $wpdb;
            $table_perfixed = $wpdb->prefix . 'comments';
            $query = new WC_Order_Query( array(
                'orderby' => 'date',
                'order' => 'DESC',
                'return' => 'ids',
            ) );
            $orders = $query->get_orders();
            foreach ( $orders as $order_id )
            {
                $results = $wpdb->get_results("
                                SELECT * FROM  $table_perfixed WHERE  `comment_post_ID` = $order_id
                                AND  `comment_type` LIKE  'order_note'
                                AND  `comment_approved` != 'trash' ORDER BY `comment_date` DESC");

                $table_perfixed_second = $wpdb->prefix . 'commentmeta';
                foreach($results as $note)
                {
                    $results = $wpdb->get_results("
                                SELECT * FROM  $table_perfixed_second WHERE  `comment_id` = $note->comment_ID
                                AND  `meta_key` LIKE  'is_customer_note'
                             ");
                    if(empty($results))
                    {
                        continue;
                    }
                    if(!isset( $order_notes[$order_id] ))
                    {
                        $order_notes[$order_id] = [];
                    }
                    $order_notes[$order_id]['order_date'] = wc_get_order($order_id)->order_date;
                    $order_notes[$order_id]['customer_id'] = wc_get_order($order_id)->get_customer_id();
                    $order_notes[$order_id]['items'] = wc_get_order($order_id)->get_items();
                }
            }

            //處理分頁問題
            $page_number = 1;
            if(isset($_REQUEST['page_number']) && is_numeric(($_REQUEST['page_number'])))
            {
                $page_number = $_REQUEST['page_number'];
            }
            $per_page = 10;
            $offset = ($page_number-1) * $per_page;
            $total_rows = count($order_notes);
            $total_pages = ceil($total_rows / $per_page); //在下方引入檔案時，會使用到。
            $index = 0;
            $tmp_order_notes = $order_notes;
            //因為此陣列的key是訂單編號，所以不方便使用for迴圈。
            foreach($tmp_order_notes as $key => $value)
            {
                $index +=1;
                if(!($index > $offset && $index <= $offset + $per_page))
                {
                    unset($order_notes[$key]);
                }
            }
            //處理分頁問題

            include_once plugin_dir_path(__FILE__) . '/includes/'.$this->setting_parameters['list_slug'].'.php' ;
        }

        public function order_contact_edit()
        {
//            以下是外部來源
            wp_register_style($this->setting_parameters['plugin_name'].'-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            wp_enqueue_style($this->setting_parameters['plugin_name'].'-bootstrap-css');

            wp_register_style($this->setting_parameters['plugin_name'].'-bootstrap-switch-css', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css');
            wp_enqueue_style($this->setting_parameters['plugin_name'].'-bootstrap-switch-css');

            wp_register_script($this->setting_parameters['plugin_name'].'bootstrap-switch-js', 'https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js');
            wp_enqueue_script($this->setting_parameters['plugin_name'].'bootstrap-switch-js');

            //做Delete的動作
            if(isset($_REQUEST['order_id']) && isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
            {
                $order_notes = $this->get_order_notes($_REQUEST['order_id']);
                if(!empty($order_notes))
                {
                    foreach($order_notes as $note)
                    {
                        wp_delete_comment($note['note_id'], true);
                    }
                }
                wp_redirect( admin_url( '/admin.php?page='.$this->setting_parameters['list_slug'] ) );
            }else {
                global $wpdb;
                $query = new WC_Order_Query( array(
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'return' => 'ids',
                ) );
                $orders = $query->get_orders();
                include_once (plugin_dir_path(__FILE__).'/includes/'.$this->setting_parameters['edit_slug'].'.php');
            }
        }

        private function get_order_notes($order_id)
        {
            global $wpdb;

            $table_perfixed = $wpdb->prefix . 'comments';
            $results = $wpdb->get_results("
                                SELECT * FROM   $table_perfixed WHERE  `comment_post_ID` = $order_id 
                                AND  `comment_type` LIKE  'order_note'
                                AND  `comment_approved` != 'trash' ORDER BY `comment_date` DESC ");

            $table_perfixed_second = $wpdb->prefix . 'commentmeta';
            foreach($results as $note)
            {
                $results = $wpdb->get_results("
                                SELECT * FROM   $table_perfixed_second WHERE  `comment_id` = $note->comment_ID 
                                AND  `meta_key` LIKE  'is_customer_note'
                             ");
                if(empty($results))
                {
                    continue;
                }
                $order_notes[]  = array(
                    'note_id'      => $note->comment_ID,
                    'note_date'    => $note->comment_date,
                    'note_author'  => $note->comment_author,
                    'note_content' => $note->comment_content,
                );
            }

            return $order_notes;
        }
    }

    $GLOBALS['WC_Order_Contact'] = WC_Order_Contact::get_instance();

endif;