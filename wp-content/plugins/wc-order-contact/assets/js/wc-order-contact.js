jQuery(function ($)
{

    $(document).on('click', '.btn.btn-danger', function (e) {
        e.preventDefault();
        if( confirm( "Are you sure want to continue?" ) )
        {
            $(this).parents('form').submit();
        }
    });

    $(document).on('click', '#email_activated_switch_label', function ()
    {
        $(this).next('.switch.btn').trigger('click');
    });

    $(document).on('change', 'input[name="email_activated"]', function ()
    {
        $(this).parents('form').first().submit();
    });

    $.each($('#order_id'), function(key, value)
    {
       if($(value).parent('form').hasClass($(value).data('change-url')))
       {
           $(value).select2();
       }
    });

   $('#order_id').on('change', function (e)
   {
       //確認是否在此外掛的編輯頁
     if($(this).parent('form').hasClass($(this).data('change-url')))
     {
         e.preventDefault();
         location.href = location.origin + location.pathname + '?page='+$(this).data('change-url')+'&order_id='+$(this).val();
     }
   });

});