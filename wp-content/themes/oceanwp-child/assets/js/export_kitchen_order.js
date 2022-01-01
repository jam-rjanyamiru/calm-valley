jQuery(function ($){
    minDate = new Date();
    minDate.setDate(minDate.getDate() - 60);
    maxDate = new Date();
    maxDate.setDate(maxDate.getDate());
    $('input[name="start_date"]').datepicker({
        minDate: minDate,
        maxDate: maxDate,
        language: 'zh',
    });

    $('input[name="end_date"]').datepicker({
        minDate: minDate,
        maxDate: maxDate,
        language: 'zh',
    });
})
