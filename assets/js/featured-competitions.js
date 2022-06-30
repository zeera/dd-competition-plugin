jQuery(document).ready(function ($) {

    // function getCounterData(obj) {
    //     var days = parseInt($('.e-m-days', obj).text());
    //     var hours = parseInt($('.e-m-hours', obj).text());
    //     var minutes = parseInt($('.e-m-minutes', obj).text());
    //     var seconds = parseInt($('.e-m-seconds', obj).text());
    //     return seconds + (minutes * 60) + (hours * 3600) + (days * 3600 * 24);
    // }

    // function setCounterData(s, obj) {
    //     var days = Math.floor(s / (3600 * 24));
    //     var hours = Math.floor((s % (60 * 60 * 24)) / (3600));
    //     var minutes = Math.floor((s % (60 * 60)) / 60);
    //     var seconds = Math.floor(s % 60);

    //     console.log(days, hours, minutes, seconds);

    //     $('.e-m-days', obj).html(days);
    //     $('.e-m-hours', obj).html(hours);
    //     $('.e-m-minutes', obj).html(minutes);
    //     $('.e-m-seconds', obj).html(seconds);
    // }

    // var count = getCounterData($(".countdown"));

    // var timer = setInterval(function() {
    //     count--;
    //     if (count == 0) {
    //     clearInterval(timer);
    //     return;
    //     }
    //     setCounterData(count, $(".countdown"));
    // }, 1000);

    if ($('.competition-listing-section').length > 0) {
        $('.competition-listing-section .competition-item').each(function (e) {
            var targetDate = $(this).data('enddate');
            const days = $(this).find(".e-m-days");
            const hours = $(this).find(".e-m-hours");
            const minutes = $(this).find(".e-m-minutes");
            const seconds = $(this).find(".e-m-seconds");
            if (targetDate != '') {
                const targetDateFormatted = new Date(targetDate);
                var winInterval = window.setInterval( function() {
                    // Where we check if 'now' is greater than the target date
                    var date = Date.now();
                    console.log(date);
                    console.log(targetDate);
                    console.log(targetDateFormatted);
                    if (date > targetDateFormatted)
                    {
                        // Where we break
                        console.log("Expired");
                        days.text('---');
                        hours.text('---');
                        minutes.text('---');
                        seconds.text('---');
                        clearInterval(winInterval);
                    } else
                    {
                        // Where we set values
                        var millis = targetDateFormatted - date;
                        var millisObject = convertMillis(millis);

                        // Display values in HTML
                        days.text(millisObject.d);
                        hours.text(millisObject.h);
                        minutes.text(millisObject.m);
                        seconds.text(millisObject.s);
                    };
                }, 1000);
            } else {
                days.text('---');
                hours.text('---');
                minutes.text('---');
                seconds.text('---');
                clearInterval(winInterval);
            }
        });
    }

    function convertMillis(milliseconds, format) {
        var days, hours, minutes, seconds, total_hours, total_minutes, total_seconds;

        total_seconds = parseInt(Math.floor(milliseconds / 1000));
        total_minutes = parseInt(Math.floor(total_seconds / 60));
        total_hours = parseInt(Math.floor(total_minutes / 60));
        days = parseInt(Math.floor(total_hours / 24));

        seconds = parseInt(total_seconds % 60);
        minutes = parseInt(total_minutes % 60);
        hours = parseInt(total_hours % 24);

        switch(format) {
        case 's':
            return total_seconds;
        case 'm':
            return total_minutes;
        case 'h':
            return total_hours;
        case 'd':
            return days;
        default:
            return { d: days, h: hours, m: minutes, s: seconds };
        }
    };

});

