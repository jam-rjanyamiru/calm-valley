<?php
$global_order_contact_email_activated = get_option('global_order_contact_email_activated');
?>
<div class="wrap" id="order_contact_center">
    <h4><?=__('Order Contact', $this->setting_parameters['plugin_name'])?></h4>
    <form method="POST" action="/wc-api/modify_email_activated">
        <input type="hidden" name="action" value="modify_email_activated">
        <label id="email_activated_switch_label"><?=__('Default Switch Email Activated', $this->setting_parameters['plugin_name'])?></label>
        <input data-switch-scope="global" type="checkbox" name="email_activated" data-toggle="switchbutton" data-onstyle="success" data-offstyle="danger" value="1" <?=$global_order_contact_email_activated==1?'checked':''?>>
    </form>
    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th><?=__('Order ID', $this->setting_parameters['plugin_name'])?></th>
                <th><?=__('Order Date', $this->setting_parameters['plugin_name'])?></th>
                <th><?=__('Customer ID', $this->setting_parameters['plugin_name'])?></th>
                <th><?=__('Items', $this->setting_parameters['plugin_name'])?></th>
                <th><?=__('Email Activate', $this->setting_parameters['plugin_name'])?></th>
                <th><?=__('Actions', $this->setting_parameters['plugin_name'])?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($order_notes as $order_id => $content)
            {
                echo '<tr>';
                echo '<td><a href="'.get_edit_post_link($order_id).'">'.$order_id.'</a></td>';
                echo '<td>'.$content['order_date'].'</td>';
                echo '<td><a href="'.get_edit_user_link($content['customer_id']).'">'.$content['customer_id'].'</a></td>';
                echo '<td>';
                echo '<p>';
                foreach($content['items'] as $item_value)
                {
                    echo __('Product ID', $this->setting_parameters['plugin_name']).'：<a href="'.get_edit_post_link($item_value['product_id']).'">'.$item_value['product_id'].'</a>'.'<br>';
                    echo __('Product Name', $this->setting_parameters['plugin_name']).'：'.$item_value['name'].'<br>';
                    echo __('Product Quantity', $this->setting_parameters['plugin_name']).'：'.$item_value['quantity'].'<br>';
                    echo __('Product Subtotal', $this->setting_parameters['plugin_name']).'：'.$item_value['subtotal'].'<br>';
                }
                if(get_post_meta($order_id, 'order_contact_email_activated', 1) == 1)
                {
                    $order_contact_email_activated = 'checked';
                }else{
                    $order_contact_email_activated = '';
                }
                echo '</p>';
                echo '</td>';
                echo '<td><form class="form_modify_email_activated" name="POST" action="/wc-api/modify_email_activated"><input type="hidden" name="order_id" value="'.$order_id.'"><input type="hidden" name="action" value="modify_email_activated"><input type="checkbox" name="email_activated" data-toggle="switchbutton" data-onstyle="outline-success" data-offstyle="outline-danger" value="1" '.$order_contact_email_activated.'></form></td>';
                echo '<td>';
                echo '<form method="PUT" action="'.admin_url('admin.php').'"><input type="hidden" name="page" value="'.$this->setting_parameters['edit_slug'].'"><input type="hidden" name="order_id" value="'.$order_id.'"><input class="btn btn-info" type="submit" value="'.__('Edit', $this->setting_parameters['plugin_name']).'"></form>';
                echo '<br />';
                echo '<form method="DELETE" action="'.admin_url('admin.php').'"><input type="hidden" name="page" value="'.$this->setting_parameters['edit_slug'].'"><input type="hidden" name="action" value="delete"><input type="hidden" name="order_id" value="'.$order_id.'"><input class="btn btn-danger" type="submit" value="'.__('Delete', $this->setting_parameters['plugin_name']).'"></form>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <ul class="pagination">
        <li><a href="?page=<?=$this->setting_parameters['list_slug']?>&page_number=1"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-double-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg></a></li>
        <li class="<?php if($page_number <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($page_number <= 1){ echo '#'; } else { echo "?page=".$this->setting_parameters['list_slug']."&page_number=".($page_number - 1); } ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5.5a.5.5 0 0 0 0-1H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5z"/>
                </svg></a>
        </li>
        <li class="<?php if($page_number >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($page_number >= $total_pages){ echo '#'; } else { echo "?page=".$this->setting_parameters['list_slug']."&page_number=".($page_number + 1); } ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-11.5.5a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5z"/>
                </svg></a>
        </li>
        <li><a href="?page=<?=$this->setting_parameters['list_slug']?>&page_number=<?php echo $total_pages; ?>"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-double-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                    <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg></a></li>
    </ul>
</div>
