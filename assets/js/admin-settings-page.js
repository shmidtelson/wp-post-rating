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






