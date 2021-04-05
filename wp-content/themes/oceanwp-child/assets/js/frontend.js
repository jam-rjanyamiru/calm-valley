jQuery(function ($){

    if($('.search_camping_cart_book_record_form').length){
        $('.search_camping_cart_book_record_form .submit-btn').click(function (e){
            e.preventDefault();

            console.log('click!!')

            if(
                $('.search_camping_cart_book_record_form input[name="phone"]').val() != ''
                || $('.search_camping_cart_book_record_form input[name="birth"]').val() != ''
            )
            $.ajax({
                url: '/wc-api/filter_camping_cart_book_record',
                type: "POST",
                data:{
                    "from":'filter_camping_cart_book_record',
                    "phone":$('.search_camping_cart_book_record_form input[name="phone"]').val(),
                    "birth":$('.search_camping_cart_book_record_form input[name="birth"]').val(),
                }
            }).success(function (msg) {
                console.log(msg);
                if (msg != '') {
                    if( $('.search-result-div').length){
                        $('.search-result-div').remove();
                    }
                    $('.search_camping_cart_book_record_form').after(msg);
                }
            })
        });
    }

    $('input[name="user_is_same"]').change(function (){
        if ($(this).val() == 'y') {
            column_arr = ['first_name', 'last_name', 'id_card', 'gender', 'birth', 'phone',
            'address_1'];
            $.each(column_arr, function (key ,column){
                if( $('input[name="billing_' + column + '"]').attr('type') == 'text'
                || $('input[name="billing_' + column + '"]').attr('type') == 'tel') {
                    if( $('input[name="billing_' + column + '"]').val() != '' ){
                        $('input[name="user_' + column + '"]').val($('input[name="billing_' + column + '"]').val());
                    }
                }else if( $('input[name="billing_' + column + '"]').attr('type') == 'radio' ) {
                    radio_checked_val = $('input[name="billing_' + column + '"]:checked').val();
                    $('input[name="user_'+ column +'"][value="' + radio_checked_val + '"]').prop('checked', true);
                }
            })
        }
    })

});