<?php
if(isset($_SESSION['booking_pds'])){
    ?>
    <script src="/wp-content/themes/oceanwp-child/assets/js/booking_step_two.js"></script>
    <div class="booking-step-two">
        露營車項目
    <?php
    foreach($_SESSION['booking_pds'] as $index => $pd_id){
        ?><div class="cart-item item-<?=$index?>" data-pd-id="<?=$pd_id?>"><?php
        $product = wc_get_product($pd_id);
        if( $product->is_type( 'variable' ) ){
            $values = wc_get_product_terms( $product->get_id(), 'pa_max_people', array( 'fields' => 'all' ) );
            ?>
            <form>
                <div class="max_people">
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