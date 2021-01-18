<div class="wrap">
    <h4><?=__('Order Contact Edit', $this->setting_parameters['plugin_name'])?></h4>
    <form method="GET" class="<?=$this->setting_parameters['edit_slug']?>">
        <label for="order_id"><?=__('Order ID', $this->setting_parameters['plugin_name'])?></label>
        <select data-change-url="<?=$this->setting_parameters['edit_slug']?>" id="order_id" name="order_id">
            <option value=""><?=__('Not Selected', $this->setting_parameters['plugin_name'])?></option>
            <?php
            foreach ( $orders as $order_id )
            {
                echo '<option value="'.$order_id.'"';
                if($_REQUEST['order_id'] == $order_id)
                {
                    echo 'selected';
                }
                echo '>';
                echo $order_id;
                echo '</option>';
            }
            ?>
        </select>
    </form>
    <div class="order-qa woocommerce">
        <h4><?=__('Order Comment', $this->setting_parameters['plugin_name'])?></h4>
        <div class="order-qa-area clearfix">
        <?php
        if(isset($_REQUEST['order_id']))
        {
            if(get_post_meta($_REQUEST['order_id'], 'order_contact_email_activated', 1) == 1)
            {
                $order_contact_email_activated = 'checked';
            }else{
                $order_contact_email_activated = '';
            }
            ?>
            <div>
                <form method="POST" action="/wc-api/modify_email_activated">
                    <input type="hidden" name="from" value="edit_page">
                    <input type="hidden" name="action" value="modify_email_activated">
                    <input type="hidden" name="order_id" value="<?=$_REQUEST['order_id']?>">
                    <label id="email_activated_switch_label"><?=__('Switch Email Activated', $this->setting_parameters['plugin_name'])?></label>
                    <input type="checkbox" name="email_activated" data-toggle="switchbutton" data-onstyle="success" data-offstyle="danger" value="1" <?=$order_contact_email_activated?>>
                </form>
            </div>
            <?php
            $order_notes = $this->get_order_notes($_REQUEST['order_id']);

            //處理分頁問題
            $page_number = 1;
            if(isset($_REQUEST['page_number']) && is_numeric($_REQUEST['page_number']))
            {
                $page_number = $_REQUEST['page_number'];
            }
            $per_page = 10;
            $offset = ($page_number-1) * $per_page;
            $total_rows = count($order_notes);
            $index = 0;
            $tmp_order_notes = $order_notes;
            foreach($tmp_order_notes as $key => $value)
            {
                $index +=1;
                if(!($index > $offset && $index <= $offset + $per_page))
                {
                    unset($order_notes[$key]);
                }
            }
            $total_pages = ceil($total_rows / $per_page);
            //處理分頁問題

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
                }else {
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
        }
        ?>
        </div>
        <form action="/wc-api/save_order_comments" method="post">
            <p class="form-row">
                <textarea name="order_qa_comments" class="input-text" id="order_qa_comments" rows="8" placeholder="<?=__('Please type your words...', $this->setting_parameters['plugin_name'])?>"></textarea>
            </p>
            <input type="hidden" name="from" value="backend">
            <input type="hidden" name="order_id" value="<?=$_REQUEST['order_id'];?>">
            <p class="clearfix"><input type="submit" class="woocommerce-Button button" name="submit" value="<?=__('Update Comment', $this->setting_parameters['plugin_name'])?>"></p>
        </form>
        <ul class="pagination">
            <li><a href="?page=<?=$this->setting_parameters['edit_slug']?>&page_number=1&order_id=<?=$_REQUEST['order_id']?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-double-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg></a></li>
            <li class="<?php if($page_number <= 1){ echo 'disabled'; } ?>">
                <a href="<?php if($page_number <= 1){ echo '#'; } else { echo "?page=".$this->setting_parameters['edit_slug']."&page_number=".($page_number - 1)."&order_id=".$_REQUEST['order_id']; } ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5.5a.5.5 0 0 0 0-1H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5z"/>
                    </svg></a>
            </li>
            <li class="<?php if($page_number >= $total_pages){ echo 'disabled'; } ?>">
                <a href="<?php if($page_number >= $total_pages){ echo '#'; } else { echo "?page=".$this->setting_parameters['edit_slug']."&page_number=".($page_number + 1)."&order_id=".$_REQUEST['order_id']; } ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-11.5.5a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5z"/>
                    </svg></a>
            </li>
            <li><a href="?page=<?=$this->setting_parameters['edit_slug']?>&page_number=<?php echo $total_pages."&order_id=".$_REQUEST['order_id']; ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-double-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg></a></li>
        </ul>
    </div>
</div>