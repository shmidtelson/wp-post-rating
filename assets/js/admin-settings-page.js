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
    var rgb = "#", c, i;
    for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i * 2, 2), 16);
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
        rgb += ("00" + c).substr(c.length);
    }

    return rgb;
}

window.onload = function () {
    var selectBoxLogic = function () {
        var _this = this;

        this.selectBoxValue = document.querySelector('#wpr_position');
        this.shortcodeClass = document.querySelector('.shortcode-checked-js');

        this.showBlock = function (value) {
            this.shortcodeClass.style.display = "none";

            if (value === 'shortcode') {
                this.shortcodeClass.style.display = "block";
            }
        };

        this.selectBoxValue.onchange = function () {
            _this.showBlock(this.options[this.selectedIndex].value);
        };


        this.showBlock(this.selectBoxValue.value);
    };


    selectBoxLogic();
};

jQuery(document).ready(function ($) {
    var mainColor = $('.color_chooser_js');
    var secondColor = $('.second_color_chooser_js');
    var textColor = $('.text_color_chooser_js');
    var textBackgroundColor = $('.text_background_color_chooser_js');

    var setCssVariables = function () {
        document.documentElement.style.setProperty('--wpr-main-color', mainColor.val());
        document.documentElement.style.setProperty('--wpr-second-color', secondColor.val());
        document.documentElement.style.setProperty('--wpr-text-color', textColor.val());
        document.documentElement.style.setProperty('--wpr-text-background-color', textBackgroundColor.val());
    }

    setCssVariables()


    var myOptions = {
        // функция обратного вызова, срабатывающая каждый раз
        // при выборе цвета (когда водите мышкой по палитре)
        change: function () {
            setCssVariables()
            secondColor.val(ColorLuminance(mainColor.val(), -0.2))
        },
    };
    mainColor.wpColorPicker(myOptions);

    var myOptions = {
        change: function () {
            setCssVariables()
        },
    }
    textColor.wpColorPicker(myOptions);
    textBackgroundColor.wpColorPicker(myOptions);

});
