// Counts the number of decimal places in a floating point number
function countDecimals(number) {
    // make sure the input is correct
    if(number == undefined || Math.floor(number) === number) return 0;

    // split the number based on the first decimal, number of decimal places is equal to the length of this
    const decimal = number.toString().split(".")[1];

    // happens when there is no . in the number
    if(decimal == undefined) return 0;

    // Google sheets likes to return numbers with lots of decimals sometimes, just going to limit it to 2 instead
    //return decimal.length;

    return (decimal.length < 2 ? decimal.length : 2);
}

// Checks to see if a number (string) has commas in it
function hasCommas(number) {
    if(number == undefined || number < 1000) return false;

    const split = number.split(',')

    if(split == undefined || split.length < 1) return false;

    return true;
}

// checks if the element is on the screen
function isInViewport(element) {
    var eTop = jQuery(element).offset().top;
    var eBottom = eTop + jQuery(element).outerHeight();

    var vTop = jQuery(window).scrollTop();
    var vBottom = vTop + jQuery(window).height();

    return eBottom > vTop && eTop < vBottom;
}

function setupCounter(element) {
    const countGoal = jQuery(element).attr('countGoal'); // the number it should count up to
    const numDecimals = countDecimals(countGoal); // how many decimal places should we go to
    const duration = parseInt(jQuery(element).attr('duration')); // the time (ms) to get to the number
    const hadCommas = hasCommas(countGoal); // did the input have commas
    const prefix = jQuery(element).attr('prefix') || "";
    const suffix = jQuery(element).attr('suffix') || "";

    jQuery(element).prop('Counter', 0).animate({
        Counter: parseFloat(countGoal.replace(/,/g, ''), 10) // gets text from element, removes commas, parses as float
    }, {
        duration: duration || 4000,
        easing: 'easeOutQuad', // pulled from jquery.easing.js
        step: function (now) {
            const num = now.toFixed(numDecimals).toString(); // keeps the decimal places

            if(hadCommas) {
                jQuery(element).text(prefix + num.replace(/\B(?=(\d{3})+(?!\d))/g, ",") + suffix); // adds commas back to the string
            } else {
                jQuery(element).text(prefix + num + suffix);
            }
        }
    });
}

function checkCounters() {
    jQuery('.google-sheets-counter').each(function() {
        if(isInViewport(this))
            setupCounter(this);
    });
}

jQuery(window).on("resize scroll", checkCounters)

jQuery( document ).ready( checkCounters );