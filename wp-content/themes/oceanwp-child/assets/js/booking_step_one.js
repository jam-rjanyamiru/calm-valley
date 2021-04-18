jQuery(function ($){
    window.onload = function (){
        $('.progress .progress-bar.progress-bar-striped').eq(0).css('width', '16.6%');
        $('.progress .progress-bar.progress-bar-striped').eq(0).attr('aria-valuenow', '16.6');

        $('#simpleTipModal').modal({ backdrop: 'static', keyboard: false});
        $('.close-modal-btn').click(function (){
            $('.progress .progress-bar.progress-bar-striped').eq(1).css('width', '16.6%');
            $('.progress .progress-bar.progress-bar-striped').eq(1).attr('aria-valuenow', '16.6');
        })

        minDate = new Date();
        minDate.setDate(minDate.getDate() + 1);
        maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 60);
        $('input[name="start_date"]').datepicker({
            minDate: minDate,
            maxDate: maxDate
        })
            .focusout(function (){
                check_available_camper();
            });
        $('input[name="start_date"]').data('datepicker').selectDate(minDate);

        $('select[name="days"], input[name="is_taking_pet"]').focusout(function (){
            check_available_camper();
        });


        $('.camping-position').click(function (){
            result_string = $(this).attr('data-pd-id');
            this_data_pd_id = $(this).attr('data-pd-id');
            if(result_string != ""){
                if($('.camping-position.selected').length){
                    $('.show_select_cart .item').remove();
                    $('input[name="select_cart"]').val('');
                    $('.camping-position.selected').removeClass('selected');
                }

                $(this).addClass('selected');
                if($('input[name="select_cart"]').val() != ""){
                    result_string = ',' + result_string;
                    tmp_select_cart_val = $('input[name="select_cart"]').val();
                    if($.inArray(this_data_pd_id.toString() , $('input[name="select_cart"]').val().split(',')) == -1){
                        $('input[name="select_cart"]').val( tmp_select_cart_val + result_string);
                    }else{
                        tmp_arr = $('input[name="select_cart"]').val().split(',');
                        tmp_arr = $.grep(tmp_arr, function(value) {
                            return value != this_data_pd_id;
                        });
                        $('input[name="select_cart"]').val(tmp_arr.join());
                    }
                }else{
                    $('input[name="select_cart"]').val($('input[name="select_cart"]').val() + result_string);
                }


                if(!$('.show_select_cart .item').length){
                    $('.show_select_cart').append('<div class="item pd-' + this_data_pd_id+'">' + '商品ID:' + this_data_pd_id + '</div>');
                }else{
                    if($('.show_select_cart .item.pd-' + this_data_pd_id).length){
                        $('.show_select_cart .item.pd-' + this_data_pd_id).remove();
                    }else{
                        $('.show_select_cart').append('<div class="item pd-' + this_data_pd_id + '">' + '商品ID:' + this_data_pd_id + '</div>');
                    }
                }

                $.ajax({
                    url: '/wc-api/add_custom_data_to_cart_step_one',
                    data: {
                        "product_id" :  $('input[name="select_cart"]').val(),
                    },
                    type: "POST"
                })
            }
        });

        $('.to-step-two-btn').click(function (){

            old_date = new Date($('input[name="start_date"]').val());
            end_date = new Date(old_date);
            end_date.setDate(parseInt(end_date.getDate()) + parseInt($('select[name="days"]').val()));
            dd = end_date.getDate();
            mm = end_date.getMonth()+1;
            yyyy = end_date.getFullYear();
            if(dd < 10)
            {
                dd = '0' + dd;
            }
            if(mm < 10)
            {
                mm = '0' + mm;
            }
            end_date = yyyy + '-' + mm + '-' + dd;

            if($('input[name="select_cart"]').val() != '') {
                $.ajax({
                    url: '/wc-api/change_camping_content',
                    type: "POST",
                    data:{
                        "to":"two",
                        "start_date":$('input[name="start_date"]').val(),
                        "end_date":end_date,
                        "days":$('select[name="days"]').val(),
                    }
                }).success(function (msg) {
                    if(msg != ''){
                        $('.mapDiv01').remove();
                        $('.mapDiv02').remove();
                        $('.mainDiv').children().not('.progress').remove();
                        $('.mainDiv').append(msg);
                        $('.progress .progress-bar.progress-bar-striped').eq(2).css('width', '16.6%');
                        $('.progress .progress-bar.progress-bar-striped').eq(2).attr('aria-valuenow', '16.6');
                    }
                })
            } else {
                alert('您未選取位置，請選擇露營車');
            }

        });
    }

    function check_available_camper(){
        if(  $('.mapDiv02 .result').length ){
            console.log('refresh');
            $('.show_select_cart .item').remove();
            $('input[name="select_cart"]').val('');
            $('.camping-position.selected').removeClass('selected');
            $('.camping-position').css('background', 'white').attr('data-pd-id', '');
            $('.mapDiv02 .result').css('background', 'white').text('');
        }

        if (
            $('input[name="start_date"]').val() === undefined
            || $('input[name="start_date"]').val() == ''
        ){
            $('.mapDiv02 .result').text('您好，請選擇入住日期');
            $('.mapDiv02 .result').css('background', 'red');

            return;
        }

        $.ajax({
            url:'/wc-api/available_camper',
            method :'post',
            data:{
                'start_date':$('input[name="start_date"]').val(),
                'days':$('select[name="days"]').val(),
                'is_taking_pet':$('input[name="is_taking_pet"]:checked').val(),
            }
        }).success(function (msg) {
            let tmp_available_camper_obj = JSON.parse(msg);
            $('.mapDiv02 .result').text(msg).css('background', 'greenyellow');

            let available_camper_obj = {};
            $.each(tmp_available_camper_obj, function (key, value){
                available_camper_obj[key] = value.toString();
            })

            $('.camping-position').css('background', 'white').attr('data-pd-id', '');
            var pd_id = 0;
            $.each($('.camping-position'), function (key, value){
                if($.inArray($(value).data('position').toString(), Object.values(available_camper_obj)) !== -1){

                    $.each(available_camper_obj, function (obj_key ,obj_value){
                      if(obj_value == $(value).data('position').toString()) {
                          pd_id = obj_key;
                          return false;
                      }
                    })

                    $(value).css('background', 'greenyellow');
                    $(value).attr('data-pd-id', pd_id);
                }
            });
        });
    }
})