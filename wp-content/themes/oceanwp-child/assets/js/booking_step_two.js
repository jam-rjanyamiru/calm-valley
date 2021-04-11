jQuery(function ($){
    $('.to-step-three-btn').click(function (){
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

});