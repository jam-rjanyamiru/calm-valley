jQuery(function ($){
    var setting_date = $('#setting_date').datepicker({
        language: 'zh',
        multipleDates: true,
        multipleDatesSeparator: ', ',
    }).data('datepicker');

    var seleted_value = $('#setting_date').val();
    if ($.isArray(seleted_value.split(','))) {
        seleted_value.split(',').forEach(function(value) {
            setting_date.selectDate(new Date(value));
        })
    }
})
