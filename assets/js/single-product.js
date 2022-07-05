const allRanges = document.querySelectorAll(".range-wrap");
allRanges.forEach(wrap => {
    const range = wrap.querySelector(".slider-quantity");
    const bubble = wrap.querySelector(".bubble");

    range.addEventListener("input", () => {
        setBubble(range, bubble);
    });
    setBubble(range, bubble);
});

function setBubble(range, bubble) {
    const val = range.value;
    const min = range.min ? range.min : 0;
    const max = range.max ? range.max : 100;
    const newVal = Number(((val - min) * 100) / (max - min));
    const label = (val > 1) ? `${val} Tickets` : `${val} Ticket`;
    bubble.innerHTML = label;

    // Sorta magic numbers based on size of the native UI thumb
    bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
}

jQuery(document).ready(function ($) {
    $("#minus-quantity").click(function(e) {
        e.preventDefault();
        zoom("out");
        const allRanges = document.querySelectorAll(".range-wrap");
        allRanges.forEach(wrap => {
            const range = wrap.querySelector(".slider-quantity");
            const bubble = wrap.querySelector(".bubble");

            range.addEventListener("input", () => {
            setBubble(range, bubble);
            });
            setBubble(range, bubble);
        });
    });

    $("#plus-quantity").click(function(e) {
        e.preventDefault();
        zoom("in");
        const allRanges = document.querySelectorAll(".range-wrap");
        allRanges.forEach(wrap => {
            const range = wrap.querySelector(".slider-quantity");
            const bubble = wrap.querySelector(".bubble");

            range.addEventListener("input", () => {
            setBubble(range, bubble);
            });
            setBubble(range, bubble);
        });
    });

    function zoom(direction) {
        var slider = $(".slider-quantity");
        var step = parseInt(slider.attr('step'), 10);
        var currentSliderValue = parseInt(slider.val(), 10);
        var newStepValue = currentSliderValue + step;

        if (direction === "out") {
            newStepValue = currentSliderValue - step;
        } else {
            newStepValue = currentSliderValue + step;
        }

        slider.val(newStepValue).change();
    };

    if ($('.countdown-shortcode-wrap').length > 0) {
        var targetDate = $('.cdjs').data('enddate');
        const days = $('.cdjs').find(".e-m-days");
        const hours = $('.cdjs').find(".e-m-hours");
        const minutes = $('.cdjs').find(".e-m-minutes");
        const seconds = $('.cdjs').find(".e-m-seconds");
        if (targetDate !== '') {
            const targetDateFormatted = new Date(targetDate);
            var winInterval = window.setInterval( function() {
                // Where we check if 'now' is greater than the target date
                var date = Date.now();
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
                }
            }, 1000);
        } else {
            days.text('---');
            hours.text('---');
            minutes.text('---');
            seconds.text('---');
            clearInterval(winInterval);
        }
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
    }
});
