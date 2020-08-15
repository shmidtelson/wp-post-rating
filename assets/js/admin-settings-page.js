/**
 * Helpers
 */
function ColorLuminance(hex, lum) {
    if (hex === ''){
        return '#000000';
    }
    // validate hex string
    hex = String(hex).replace(/[^0-9a-f]/gi, '');
    if (hex.length < 6) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    lum = lum || 0;

    // convert to decimal and change luminosity
    let rgb = "#", c, i;
    for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i * 2, 2), 16);
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
        rgb += ("00" + c).substr(c.length);
    }

    return rgb;
}

jQuery(document).ready(function ($) {
    const mainColor = $('.color_chooser_js');
    const secondColor = $('.second_color_chooser_js');
    const textColor = $('.text_color_chooser_js');
    const textBackgroundColor = $('.text_background_color_chooser_js');

    const setCssVariables = function () {
        document.documentElement.style.setProperty('--wpr-main-color', mainColor.val());
        document.documentElement.style.setProperty('--wpr-second-color', secondColor.val());
        document.documentElement.style.setProperty('--wpr-text-color', textColor.val());
        document.documentElement.style.setProperty('--wpr-text-background-color', textBackgroundColor.val());
    };

    setCssVariables()


    let myOptions = {
        // функция обратного вызова, срабатывающая каждый раз
        // при выборе цвета (когда водите мышкой по палитре)
        change: function () {
            setCssVariables()
            secondColor.val(ColorLuminance(mainColor.val(), -0.2))
        },
    };
    mainColor.wpColorPicker(myOptions);

    myOptions = {
        change: function () {
            setCssVariables()
        },
    };
    textColor.wpColorPicker(myOptions);
    textBackgroundColor.wpColorPicker(myOptions);

});
