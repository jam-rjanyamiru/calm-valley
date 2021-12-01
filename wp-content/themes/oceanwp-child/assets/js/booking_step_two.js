jQuery(function ($){
    $('.to-step-three-btn').click(function (){
        var select_all = true;
        $.each($('.choose_meal'), function(key, value) {
            if (
                $(value).find('.meal-type input[type="radio"]:checked').length == 0 ||
                $(value).find('.meal-if-eat-beef input[type="radio"]:checked').length == 0 ||
                $(value).find('.meal-time input[type="radio"]:checked').length == 0
            ) {
                select_all = false;
                return false;
            }
        });
        if (select_all) {
            $.confirm({
                title: '露營車規格確認',
                animation: 'zoom',
                closeAnimation: 'scale',
                content: '貼心提醒您這次人數是' + $('select[name="max_people"]').val() + '人',
                buttons: {
                    確認: function() {
                        result_arr_data = {};
                        $.each($('.cart-item'), function (key ,value){
                            tmp_arr_data = {
                                'pd_id' : $(value).attr('data-pd-id'),
                                'max_people' : $(value).find('select[name="max_people"]').val(),

                            };
                            result_arr_data[key] = tmp_arr_data;
                        })

                        $.ajax({
                            url: '/wc-api/add_custom_data_to_cart_step_two',
                            type: "POST",
                            data:{
                                "result_arr_data":JSON.stringify(result_arr_data)
                            }
                        }).success(function (msg) {
                            if(msg != ''){
                                console.log('success');
                                console.log(msg);
                            }
                        });
                        json_data = {'to':'three'};
                        for (i=0;i<$('input[name="booking_days"]').val();i++) {
                            json_data['meal_' + i] = $('input[name="meal_' + i + '"]:checked').val();
                            json_data['eat_beef_' + i] = $('input[name="eat_beef_' + i + '"]:checked').val();
                            json_data['meal_time_' + i] = $('input[name="meal_time_' + i + '"]:checked').val();
                        }

                        $.ajax({
                            url: '/wc-api/change_camping_content',
                            type: "POST",
                            data:json_data,
                        }).success(function (msg) {
                            if(msg != ''){
                                $('.mapDiv01').remove();
                                $('.mapDiv02').remove();
                                $('.mainDiv').children().not('.progress').remove();
                                $('.mainDiv').append(msg);
                                $('.progress .progress-bar.progress-bar-striped').eq(3).css('width', '16.6%');
                                $('.progress .progress-bar.progress-bar-striped').eq(3).attr('aria-valuenow', '16.6');
                            }
                        });
                    },
                    我再想想: function() {
                        // alert('客戶還要再想想');
                    }
                }
            });
        } else {
            alert('您好，請填完選項~我才知道您要什麼規格~');
        }



    })


    const arrayToString = (arr) => {
        let str = '';
        for(let i = 0; i < arr.length; i++){
            if(Array.isArray(arr[i])){
                str += `${arrayToString(arr[i])} `;
            }else{
                str += `${arr[i]} `;
            };
        };
        return str;
    };

    Array.prototype.toObject = function() {
        const obj = {};

        // copy array elements to th object
        for (let i = 0; i < this.length; i++) {
            obj[i] = this[i];
        }

        return obj;
    };

    $('.meal-type input').change(function (){
        this_meal_input = $(this);
        this_meal_input.parents('form').find('.meal-time').css('display', 'none');
        count_day = this_meal_input.attr('name').split('_')[1];
        booking_date = this_meal_input.parents('form').find('input[name="booking_date_'+count_day+'"]').val();
        this_meal_input.parents('form').find('.meal-time input').prop('checked', false);
        this_meal_input.parents('form').find('.meal-time input').attr('disabled', true);

        if (this_meal_input.val() == 'roast') {
            this_meal_input.parents('form').find('.meal-time input').attr('disabled', false);
            this_meal_input.parents('form').find('.meal-time').css('display', 'block');
        } else {
            $.ajax({
                url: '/wc-api/check_camping_dinner_available',
                type:'post',
                data:{
                    'booking_date': booking_date,
                }
            }).success(function (msg) {
                if(msg != '') {
                    //要針對陣列做JSON.parse(可能)，或是陣列解析
                    available_time = JSON.parse(msg);

                    if(available_time.length != 0){
                        available_time.push('any');
                    }
                    is_disabled = false;
                    $.each(this_meal_input.parents('form').find('.meal-time input'), function (key ,value){
                        if($.inArray($(value).val(), available_time) === -1){
                            $(value).attr('disabled', true);
                            is_disabled = true;
                        } else {
                            $(value).attr('disabled', false);
                        }
                    });

                    if(is_disabled){
                        this_meal_input.parents('form').find('.meal-time input[value="any"]').attr('disabled', true);
                    }

                    this_meal_input.parents('form').find('.meal-time').css('display', 'block');
                }
            });
        }

    });

});
