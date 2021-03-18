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

});