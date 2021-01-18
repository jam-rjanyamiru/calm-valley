jQuery(function ($){
    $('.to-second-step').click(function (){
        $('.mainDiv').html('<form name="p2_form" id="p2_form" action="https://hayaku.com.tw/index.php/Orders/booking_p2_process" method="post">\n' +
            '\t\t\t\t\t\t\t\t\n' +
            '\t\t\t\t\t\t\t<div class="dashboard-form-style dashboard-form-lg tent_price mb-3 pb-4 mb-lg-4 pb-lg-5" id="tent_1" name="tent_1" style="display: block;"><h6><strong class="text-primary"><i class="fas fa-bed mr-1"></i>狩獵風(四人) II 帳篷</strong></h6><br><label>人數選擇</label>\n' +
            '\t\t\t\t\t\t\t\t\t    <select name="t1_tent_person" id="t1_tent_person" class="person_select"><option value="1">1人</option><option value="2">2人</option><option value="3">3人</option><option value="4" selected="selected">4人</option></select><div id="t1_person"></div>\n' +
            '\t\t\t\t\t\t\t\t\t    <input type="hidden" id="t1_person_max" name="t1_person_max" value="4">\n' +
            '\t\t\t\t\t\t\t\t\t    <input type="checkbox" id="t1_park_space" name="t1_park_space" value="1"><label for="t1_park_space">是否開車?&nbsp;&nbsp;&nbsp;&nbsp;<b>車輛可停於園區內專用停車場，車位由海崖谷為您安排。</b></label><br>\n' +
            '\t\t\t\t\t\t\t\t\t\n' +
            '\t\t\t\t\t\t\t\t\t    <label>供餐選擇</label><select name="t1_meals" id="t1_meals"><option value="HALF_DAY" selected="selected">半天餐點(只提供早餐)</option></select><div id="t1_breakfest" class="breakfest">\n' +
            '\t\t\t\t\t\t\t\t\t\t    <!--<label>早餐餐點</label> X <input name="t1_ba" type="number" min="0" max="5"></input>人份<br/>-->\n' +
            '\t\t\t\t\t\t\t\t\t\t    <div role="alert" class="milenia-alert-box milenia-alert-box--info d-inline-block py-0 mb-3">\n' +
            '\t\t\t\t\t\t\t\t\t\t\t    <div class="milenia-alert-box-inner">早餐將提供餐卷，可自行前往餐廳用餐\n' +
            '\t\t\t\t\t\t\t\t\t\t\t\t    <button type="button" class="milenia-alert-box-close">Close</button>\n' +
            '\t\t\t\t\t\t\t\t\t\t\t    </div>\n' +
            '\t\t\t\t\t\t\t\t\t\t    </div></div><hr class="milenia-divider--medium w-100 my-3">\n' +
            '\t\t\t\t\t\t\t\t\t    <div class="show_price mb-3">設施使用期間的費用：<strong class="milenia-entity-price">$ 5800 NT</strong></div>\n' +
            '\t\t\t\t\t\t\t\t    </div>\t\t\t\t\t\t\t\t\n' +
            '\t\t\t\t\t\t\t\t<!--\n' +
            '\t\t\t\t\t\t\t\t<div class="dashboard-form-style dashboard-form-lg tent_price mb-3 pb-4 mb-lg-4 pb-lg-5" id="tent_1" name="tent_1">\n' +
            '\t\t\t\t\t\t\t\t\t<div id="t1_person">\n' +
            '\t\t\t\t\t\t\t\t\t\t<input type="checkbox" id="t1_add_bed" name="t1_add_bed" value="1"><label for="t1_add_bed" style="margin-bottom: 0.8125rem;">是否再追加1人?</label>\n' +
            '\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t<input type="hidden" id="t1_person_max" name="t1_person_max" value="">\n' +
            '\t\t\t\t\t\t\t\t\t<input type="checkbox" id="t1_park_space" name="t1_park_space" value="1"><label for="t1_park_space">是否開車?</label><br>\n' +
            '\t\t\t\t\t\t\t\t\t\n' +
            '\t\t\t\t\t\t\t\t\t<div id="t1_breakfest" class="breakfest">\n' +
            '\t\t\t\t\t\t\t\t\t\t<label>早餐餐點</label> X <input name="t1_ba" type="number" min="0" max="5"></input>人份<br/>\n' +
            '\t\t\t\t\t\t\t\t\t\t<div role="alert" class="milenia-alert-box milenia-alert-box--info d-inline-block py-0 mb-5">\n' +
            '\t\t\t\t\t\t\t\t\t\t\t<div class="milenia-alert-box-inner">早餐將提供餐卷，可前自行往餐廳用餐\n' +
            '\t\t\t\t\t\t\t\t\t\t\t\t<button type="button" class="milenia-alert-box-close">Close</button>\n' +
            '\t\t\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t<div id="t1_dinner" class="dinner">\n' +
            '\t\t\t\t\t\t\t\t\t\t<div class="mb-3">\n' +
            '\t\t\t\t\t\t\t\t\t\t\t<i class="fas fa-utensils text-primary mr-2" style="vertical-align: initial;"></i>晚餐A餐 <small>(不限制)</small> × <input name="t1_da" type="number" min="0" max="5"></input> 人份\n' +
            '\t\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t\t<div class="mb-3">\n' +
            '\t\t\t\t\t\t\t\t\t\t\t<i class="fas fa-utensils text-primary mr-2" style="vertical-align: initial;"></i>晚餐B餐 <small>(不吃牛肉)</small> × <input name="t1_db" type="number" min="0" max="5"></input> 人份\n' +
            '\t\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t\t<div class="mb-3">\n' +
            '\t\t\t\t\t\t\t\t\t\t\t<i class="fas fa-utensils text-primary mr-2" style="vertical-align: initial;"></i>晚餐C餐 <small>(素食)</small> × <input name="t1_dc" type="number" min="0" max="5"></input> 人份\n' +
            '\t\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t\t\n' +
            '\t\t\t\t\t\t\t\t\t</div>\n' +
            '\t\t\t\t\t\t\t\t\t<hr class="milenia-divider--medium w-100 my-3">\n' +
            '\t\t\t\t\t\t\t\t\t<div class="show_price mb-3"></div>\n' +
            '\t\t\t\t\t\t\t\t</div>\n' +
            '                                -->\n' +
            '\n' +
            '\t\t\t\t\t\t\t\t<div id="lesson_price" name="lesson_price" class="dashboard-form-style dashboard-form-lg lesson_price mb-5 pb-4 pb-lg-5"><h6><strong class="text-primary"><i class="fas fa-clipboard-list mr-1"></i>目前開放的可參加課程</strong></h6>\n' +
            '\t\t\t\t\t\t\t\t\t<hr class="milenia-divider--medium w-100 my-3">\n' +
            '\t\t\t\t\t\t\t\t\t<div class="show_price mb-3">參加課程的費用共為：<strong class="milenia-entity-price">$ 0 NT</strong></div>\n' +
            '\t\t\t\t\t\t\t\t</div>\n' +
            '\n' +
            '\t\t\t\t\t\t\t\t<div id="total_price" name="total_price" class="dashboard-form-style dashboard-form-lg total_price  mb-3 py-4 mb-lg-4 py-lg-5"><h5 class="text-primary d-inline-block m-0"><strong>總額：<strong></strong></strong></h5><strong><strong><strong class="milenia-entity-price text-primary">$ 5800 NT</strong></strong></strong></div>\n' +
            '\n' +
            '\t\t\t\t\t\t\t\t<div class="dashboard-form-style dashboard-form-lg">\n' +
            '\t\t\t\t\t\t\t\t    <!--placehodler="請於此欄位填寫欲匯款之帳戶末五碼，方便後續款項確認，謝謝您的合作。如入住超過一晚且有不同餐點類型需求者，請於此處註(EX: 1/1烤肉、1/2火鍋)"-->\n' +
            '\t\t\t\t\t\t\t\t\t<label for="memo">其他備註項目 (100字以內)</label><br>\n' +
            '\t\t\t\t\t\t\t\t\t    \t\t\t\t\t\t\t                <textarea id="memo" name="memo" cols="100" rows="5" maxlength="100" placeholder="如有其他需求者，請於此處註明。"></textarea>\n' +
            '\t\t\t\t\t\t\t            \t\t\t\t\t\t\t\t\t<br><br>\n' +
            '\t\t\t\t\t\t\t\t\t<input type="hidden" id="tentCount" name="tentCount" value="1">\n' +
            '\t\t\t\t\t\t\t\t\t<input type="hidden" name="ci_csrf_token" value="">\n' +
            '\t\t\t\t\t\t\t\t\t<button class="milenia-btn milenia-btn--medium milenia-btn--scheme-light text-center" onclick="window.history.go(-1); return false;" name="return_p1" id="return_p1">回上一步</button>\n' +
            '\t\t\t\t\t\t\t\t\t<button class="milenia-btn milenia-btn--medium milenia-btn--scheme-primary text-center" type="submit" name="p2_sb" id="p2_sb" style="float: right;">下一步</button>\n' +
            '\t\t\t\t\t\t\t\t</div>\n' +
            '\n' +
            '\t\t\t\t\t\t\t</form>');
    })
});