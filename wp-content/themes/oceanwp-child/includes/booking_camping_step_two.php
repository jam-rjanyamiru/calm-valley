<?php
if(isset($_SESSION['booking_pds'])){
    ?>
    <!-- jquery-confirm -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script src="/wp-content/themes/oceanwp-child/assets/js/booking_step_two.js"></script>
    <div class="booking-step-two">
        <h3>露營餐點</h3>
    <?php
//    目前只會有一個商品，所以只會跑一次loop
    foreach($_SESSION['booking_pds'] as $index => $pd_id){
        ?><div class="cart-item item-<?=$index?>" data-pd-id="<?=$pd_id?>"><?php
        $product = wc_get_product($pd_id);
        if( $product->is_type( 'variable' ) ){
            $values = wc_get_product_terms( $product->get_id(), 'pa_max_people', array( 'fields' => 'all' ) );
            ?>
                <div class="max_people">
                    <?php _e('用餐人數', 'oceanwp')?>
                    <select name="max_people">
                <?php
                    foreach($values as $val){
                        ?>
                        <option value="<?=$val->slug?>"><?=$val->slug?></option>
                        <?php
                    }
                ?>
                    </select>
                    <label for="total-adult">幾個大人</label>
                    <input type="number" name="adult_total" min="0" max="2" step="1">
                    <label for="total-child">幾個小孩</label>
                    <input type="number" name="child_total" min="0" max="2" step="1">
                </div>
                <?php
                print('總共有'.$_SESSION['booking_days'].'天');?>
                <input type="hidden" name="booking_days" value="<?=$_SESSION['booking_days']?>">
                <?php
                for($i=0;$i<$_SESSION['booking_days'];$i++){
                    $tmp_date = date('Y/m/d', strtotime($_SESSION['booking_start_date'] . "+".$i." days" ));
                    ?>
                    <hr>
                    <form>
                        <div class="day_<?=$i+1?>">第<?=$i+1?>天晚餐
                            <input type="hidden" name="booking_date_<?=$i?>" value="<?=$tmp_date?>">
                            <div class="choose_meal">
                                <div class="meal-type">
                                    <?php if(get_option('dinner_item_01') != ''):?>
                                    <label for="meal-item-01-<?=$i?>"><?=get_option('dinner_item_01')?></label>
                                    <input id="meal-item-01-<?=$i?>" type="radio" name="meal_<?=$i?>" value="item_01">
                                    <?php endif;?>
                                    <?php if(get_option('dinner_item_02') != ''):?>
                                    <label for="meal-item-02-<?=$i?>"><?=get_option('dinner_item_02')?></label>
                                    <input id="meal-item-02-<?=$i?>" type="radio" name="meal_<?=$i?>" value="item_02">
                                    <?php endif;?>
                                    <label for="meal-steam-<?=$i?>">蒸煮-海鮮</label>
                                    <input id="meal-steam-<?=$i?>" type="radio" name="meal_<?=$i?>" value="steam">
                                </div>

                                <div class="meal-if-eat-beef">
                                    <label for="meal-eat-beef-<?=$i?>">吃牛</label>
                                    <input id="meal-eat-beef-<?=$i?>" type="radio" name="eat_beef_<?=$i?>" value="y">
                                    <label for="meal-dont-eat-beef-<?=$i?>">不吃牛</label>
                                    <input id="meal-dont-eat-beef-<?=$i?>" type="radio" name="eat_beef_<?=$i?>" value="n">
                                </div>

                                <div class="meal-time" style="display:none;">
                                    <label for="meal-time-period-01-<?=$i?>"><?=get_option('dinner_time_period_01')?></label>
                                    <input id="meal-time-period-01-<?=$i?>" type="radio" name="meal_time_<?=$i?>" value="time_period_01">
                                    <label for="meal-time-period-02-<?=$i?>"><?=get_option('dinner_time_period_02')?></label>
                                    <input id="meal-time-period-02-<?=$i?>" type="radio" name="meal_time_<?=$i?>" value="time_period_02">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                }?>
            <?php
            }
        ?>
        </div>
        <?php
    }
    ?>
        <a class="button wpmc-nav-button" href="<?=home_url() . '/booking'?>">重新選擇</a>
        <a class="button wpmc-nav-button to-step-three-btn">前往第三步驟按鈕</a>
    </div>
    <?php
}
