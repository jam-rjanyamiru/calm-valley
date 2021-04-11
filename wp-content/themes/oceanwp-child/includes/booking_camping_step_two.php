<?php
if(isset($_SESSION['booking_pds'])){
    ?>
    <script src="/wp-content/themes/oceanwp-child/assets/js/booking_step_two.js"></script>
    <div class="booking-step-two">
        <h3>露營車規格</h3>
    <?php
//    目前只會有一個商品，所以只會跑一次loop
    foreach($_SESSION['booking_pds'] as $index => $pd_id){
        ?><div class="cart-item item-<?=$index?>" data-pd-id="<?=$pd_id?>"><?php
        $product = wc_get_product($pd_id);
        if( $product->is_type( 'variable' ) ){
            $values = wc_get_product_terms( $product->get_id(), 'pa_max_people', array( 'fields' => 'all' ) );
            ?>
            <form>
                <div class="max_people">
                    露營車人數
                    <select name="max_people">
                <?php
                    foreach($values as $val){
                        ?>
                        <option value="<?=$val->slug?>"><?=$val->slug?></option>
                        <?php
                    }
                ?>
                    </select>
                </div>
                <?php
                print('總共有'.$_SESSION['booking_days'].'天');?>
                <input type="hidden" name="booking_days" value="<?=$_SESSION['booking_days']?>">
                <?php
                for($i=0;$i<$_SESSION['booking_days'];$i++){
                    ?>
                    <hr>
                    <div class="day_<?=$i+1?>">第<?=$i+1?>天
                        <div class="choose_meal">
                            <div class="meal-type">
                                <label for="meal-roast">燒烤</label>
                                <input id="meal-roast" type="radio" name="meal_<?=$i?>" value="roast">
                                <label for="meal-steam">蒸煮-海鮮</label>
                                <input id="meal-steam" type="radio" name="meal_<?=$i?>" value="steam">
                            </div>

                            <div class="meal-if-eat-beef">
                                <label for="meal-eat-beef">吃牛</label>
                                <input id="meal-eat-beef" type="radio" name="eat_beef_<?=$i?>" value="y">
                                <label for="meal-dont-eat-beef">不吃牛</label>
                                <input id="meal-dont-eat-beef" type="radio" name="eat_beef_<?=$i?>" value="n">
                            </div>

                            <div class="meal-time">
                                <label for="meal-time-any">不限時段</label>
                                <input id="meal-time-any" type="radio" name="meal_time_<?=$i?>" value="any">
                                <label for="meal-time-five-thirty">下午 5點30~7點</label>
                                <input id="meal-time-five-thirty" type="radio" name="meal_time_<?=$i?>" value="five_thirty">
                                <label for="meal-time-seven">下午 7點~8點30</label>
                                <input id="meal-time-seven" type="radio" name="meal_time_<?=$i?>" value="seven">
                            </div>
                        </div>
                    </div>
                    <?php
                }?>
            </form>
            <?php
            }
        ?>
        </div>
        <?php
    }
    ?>
        <button class="to-step-three-btn">前往第三步驟按鈕</button>
    </div>
    <?php
}