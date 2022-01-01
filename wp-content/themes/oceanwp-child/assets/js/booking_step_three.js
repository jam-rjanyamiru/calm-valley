jQuery(function ($){
   $('.to-step-four-btn').click(function (){

       $.confirm({
           title: '合約確認',
           animation: 'zoom',
           closeAnimation: 'scale',
           content: '貼心提醒您現在是在確認合約',
           buttons: {
               確認: function() {
                   $.ajax({
                       url: '/wc-api/change_camping_content',
                       type: "POST",
                       data:{
                           "to": "four",
                           "accept_contract": "y"
                       }
                   }).success(function (msg) {
                       if(msg != ''){
                           location.href = msg;
                       }
                   });
               },
               我再想想: function() {
                   // alert('客戶還要再想想');
               }
           }
       });
   })
});
