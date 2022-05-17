jQuery(document).ready(function ($) {
    if( $('.question-ans').length > 0 ) {
        $('.single_add_to_cart_button').addClass('disabled').attr('disabled', true).prop('disabled', true);
        $('input[name="competition_answer"]:radio').change(function () {
            var selected = $("input[name='competition_answer']:checked").val();
            localStorage.setItem('selectedAnswer', selected);
            var selectedAnswer = localStorage.getItem('selectedAnswer');
            if ( selectedAnswer != '' ) {
                $('.single_add_to_cart_button').removeClass('disabled').attr('disabled', false).prop('disabled', false);
            }
        });
    }
});
