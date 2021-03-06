jQuery(function ($){
    $('.to-step-three-btn').click(function (){
        result_arr_data = {};
        $.each($('.cart-item'), function (key ,value){
            tmp_arr_data = {
                'pd_id' : $(value).attr('data-pd-id'),
                'choose_meal' : $(value).find('select[name="choose_meal"]').val(),
                'is_driving' : $(value).find('input[name="is_driving"]:checked').val(),
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

        $.ajax({
            url: '/wc-api/change_camping_content',
            type: "POST",
            data:{
                "to":"three"
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