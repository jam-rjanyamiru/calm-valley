<h3>搜尋預約紀錄的表格</h3>
<form class="search_camping_cart_book_record_form" action="/wc-api/search_camping_cart_book_reocrd" method="POST">
    <input type="hidden" name="from" value="filter_camping_cart_book_record">
    <label for="input_phone">訂購人手機</label>(範例:請輸入台灣的手機號碼格式，像是「0912345678」)
    <br><input type="tel" id="input_phone" name="phone" maxlength="10" placeholder="範例：0912345678">
    <label for="input_birth">訂購人生日</label>(範例:請輸入台灣的生日格式，像是民國50年10月10日，那請輸入「0501010」)
    <br><input type="text" id="input_birth" name="birth" maxlength="7" placeholder="範例：0501010">
    <br><button class="submit-btn">搜尋</button>
</form>