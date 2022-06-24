jQuery(document).ready(function ($) {
    if ( $('.datetimepicker').length > 0 ) {
        $('.datetimepicker').datetimepicker();
    }
    if ( $('.colorField').length > 0 ) {
        $('.colorField').wpColorPicker();
    }
    if ( $('.selectpicker').length > 0 ) {
        var selected = $('.selectpicker').data('selected');
        $('.selectpicker').select2({
            placeholder: 'Select Product',
            allowClear: true,
        });
        $(".selectpicker").val(selected);
        $('.selectpicker').trigger('change');
    }
    $('.cashSaleIndexTable').each(function () {
        var ppp = $(this).data('ppp');
        var pppOptions = $(this).data('ppp-options');
        var pppOptionsArr = pppOptions.split(',');
        pppOptionsArr.unshift(`${ppp}`);
        $(this).DataTable({
            'pageLength': parseInt(ppp),
            'lengthMenu': pppOptionsArr,
            "responsive": true,
            'autoWidth': true,
            // "order":[],
            // 'ajax': {
            //     url: 'https://web.clickclick.media/_websites/lifestyle_merge_2021/wp-json/wp/v2/price-match',
            // },
            // 'columns': [
            //     {data : "name"},
            //     {data : "email"},
            //     {data : "competitor_price"},
            //     {data : "competitor_price_url"},
            //     {data : "status"},
            // ]
        });
    })
});
