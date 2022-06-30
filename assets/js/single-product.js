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
});
