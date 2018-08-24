// $('.icon-star').click(function() {
//     $(this).parents('.wpr-rating').find('.icon-star').removeClass('checked');
//     $(this).addClass('checked');
//
//     var submitStars = $(this).attr('data-value');
//     console.log(submitStars)
// });


var Stars = function () {
    var _this = this;

    this.stars = document.querySelectorAll(".icon-star");
    // @fixme: indexof
    this.hasClass = function (target, className) {
        return new RegExp('(\\s|^)' + className + '(\\s|$)').test(target.className);
    };

    for (i = 0; i < this.stars.length; i++) {
        this.stars[i].addEventListener('click', function () {
            var parent = this.parentElement;
            childrens = parent.children;

            for (i in childrens) {
                if (_this.hasClass(childrens[i], "checked"))
                    childrens[i].classList.remove("checked")
            }

            this.classList.add("checked");
            var submitStars = this.dataset.value;

            console.log(submitStars)
        });

    }
};


new Stars();
