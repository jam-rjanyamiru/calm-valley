jQuery(function ($){
    window.onload = function (){
        // $('#simpleTipModal').modal({ backdrop: 'static', keyboard: false});

        $('input[name="start_date"]').datepicker()
            .on('focusout', function (){
                check_available_camper();
            });

        $('select[name="days"]').change(function (){
            check_available_camper();
        });


        $('.camping-position').click(function (){
            console.log('click!!');
            jQuery.ajax({
                url: WC_VARIATION_ADD_TO_CART.ajax_url,
                data: {
                    "action" : "woocommerce_add_variation_to_cart",
                    "product_id" : "145",
                    "variation_id" : "170",
                    "quantity" : 1,
                    "variation" : {
                        "size" : "l",
                    },
                },
                type: "POST"
            });
        });

    }

    function check_available_camper(){
        $.ajax({
            url:'/wc-api/available_camper',
            method :'post',
            data:{
                'start_date':$('input[name="start_date"]').val(),
                'days':$('select[name="days"]').val(),
            }
        }).success(function (msg) {
            let tmp_available_camper_obj = JSON.parse(msg);
            $('.mapDiv02 .result').text(msg);
            $('.mapDiv02 .result').css('background', 'red');

            let available_camper_obj = {};
            $.each(tmp_available_camper_obj, function (key, value){
                available_camper_obj[key] = value.toString();
            })

            $('.camping-position').css('background', 'white');
            $('.camping-position').attr('data-pd-id', '');
            var pd_id = 0;
            $.each($('.camping-position'), function (key, value){
                if($.inArray($(value).data('position').toString(), Object.values(available_camper_obj)) !== -1){

                    $.each(available_camper_obj, function (obj_key ,obj_value){
                      if(obj_value == $(value).data('position').toString()) {
                          pd_id = obj_key;
                          return false;
                      }
                    })

                    $(value).css('background', 'red');
                    $(value).attr('data-pd-id', pd_id);
                }
            });
        });
    }
})