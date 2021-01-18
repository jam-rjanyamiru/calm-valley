<!-- 這是要客製預約頁面用的 -->
<?php
get_header();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<div class="mainDiv">

    <div class="mapDiv01 col-12 col-xl-8 order-1 order-xl-0">
        這是客製模板的DIV 01

        <table>

            <tbody class="mapTable">
            <tr>
                <td id="M8(4)_t01" data-status="0" title="狩獵風(四人) I，已客滿"><div class="unavailable"><img class="tent" src="https://hayaku.com.tw/images/not_available.png" height="42" width="42"></div></td>
            </tr>
            <tr>
                <td id="C300(4)_t07" data-status="1" title="北歐風(四人) VII"><div class="available"><img class="tent" src="https://hayaku.com.tw/images/available.png" height="42" width="42"></div></td>            </tr>
            <tr>
                <td>301</td>
            </tr>
            <tr>
                <td>401</td>
            </tr>
            <tr>
                <td>501</td>
            </tr>
            <tr>
                <td>601</td>
                <td>602</td>
                <td>603</td>
                <td id="M8(4)_t01" data-status="0" title="狩獵風(四人) I，已客滿"><div class="unavailable"><img class="tent" src="https://hayaku.com.tw/images/not_available.png" height="42" width="42"></div></td>
                <td id="C300(4)_t07" data-status="1" title="北歐風(四人) VII"><div class="available"><img class="tent" src="https://hayaku.com.tw/images/available.png" height="42" width="42"></div></td>
            </tr>
            </tbody>
        </table>
        <img class="mapImage" src="https://hayaku.com.tw/images/map_all.jpg">
    </div>

    <div class="mapDiv02 col-12 col-xl-4 order-0 order-xl-1">
        這是客製模板 DIV 02
        <div class="milenia-grid-item milenia-widget milenia-widget--check-availability">
            <div class="milenia-grid-item-inner">
                <div class="milenia-grid-item-content">
                    <div class="milenia-colorizer--scheme-dark pb-2"><div class="milenia-colorizer-bg-color"></div>
                        <h5 class="milenia-widget-title mb-0">請先選擇起始日期及天數，再於地圖點選帳篷。<br></h5>
                        <h6 class="milenia-widget-title mb-0" style="color:red">若帳篷不可選擇代表已被預訂。</h6>
                        <!--										<small class="form-caption">Required fields are followed by *</small>-->
                        <form class="milenia-booking-form milenia-booking-form--style-3" name="p1_form" id="p1_form" action="https://hayaku.com.tw/index.php/Orders/booking_p1_process" method="post">
                            <div class="form-group">
                                <div class="form-col">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-col form-col--arrival-date form-col--over">
                                    <div class="form-control">
                                        <label for="booking-form-arrival-date-3">入住日期</label><span class="milenia-field-datepicker milenia-field-datepicker--style-3">2021/01/13</span>
                                        <input type="text" id="start_date" name="start_date" autocomplete="off" readonly="" class="milenia-datepicker milenia-field-datepicker-invoker hasDatepicker">
                                    </div>
                                </div>
                                <div class="form-col">
                                    <div class="form-control">
                                        <label>入住天數</label>
                                        <div class="milenia-custom-select">
                                            <select name="Hoteldays" id="Hoteldays" style="display: none;">
                                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>													</select>
                                            <div class="milenia-selected-option">1</div><ul class="milenia-options-list milenia-list--unstyled"><li data-value="1" class="milenia-active">1</li><li data-value="2">2</li><li data-value="3">3</li><li data-value="4">4</li><li data-value="5">5</li><li data-value="6">6</li><li data-value="7">7</li></ul></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="select_tent" id="select_tent" value="">

                            <input type="hidden" name="control_check" id="control_check" value="0">
                            <input type="hidden" name="ci_csrf_token" value="">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="milenia-booking-form milenia-booking-form--style-3 mt-3 d-none d-md-block">
            <div class="form-group">
                <div class="form-col form-col--action pr-2 pr-sm-5 d-inline-block">
                    <div class="form-control">
                        <button type="button" onclick="location.href='https://hayaku.com.tw/index.php/Introduction/TentIntro';" class="milenia-btn milenia-btn--huge milenia-btn--scheme-dark" style="max-width: 200px;"><i class="fas fa-undo-alt mr-1 align-baseline"></i>返回列表</button>
                    </div>
                </div>
                <div class="form-col form-col--action d-inline-block">
                    <div class="form-control">
                        <button type="submit" name="p1_sb" id="p1_sb" class="milenia-btn milenia-btn--huge milenia-btn--scheme-primary" style="max-width: 200px;float:right;">下一步<i class="fas fa-arrow-right ml-1 align-baseline"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="milenia-grid-item milenia-widget milenia-widget--check-availability mt-3 d-none d-md-block">
            <div class="milenia-grid-item-inner">
                <div class="milenia-grid-item-content">
                    <!--顯示使用者選擇的帳篷名稱-->
                    <div id="show_select_tent">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
get_footer();