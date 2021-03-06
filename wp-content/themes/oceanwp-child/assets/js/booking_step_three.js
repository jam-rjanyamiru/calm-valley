jQuery(function ($){
   console.log('step three');
   $('.to-step-four-btn').click(function (){
       $.ajax({
           url: '/wc-api/change_camping_content',
           type: "POST",
           data:{
               "to":"four"
           }
       }).success(function (msg) {
           if(msg != ''){
                alert('到結帳頁面填寫訂購人資訊囉');
                location.href = msg;
           }
       });
   })
});