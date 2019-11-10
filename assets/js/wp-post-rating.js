var AjaxSendRequest = function () {
    var that = this;
    this.ajax_response = '';
    this.ajaxVars = {
        ajaxurl: "/wp-admin/admin-ajax.php",
        nonce: document.querySelector("meta[name='_wpr_nonce']").getAttribute("content")
    };
    this.request = new XMLHttpRequest();

    this.ajax_vote = function (post_id, vote) {
        var data = {
            'action': 'wpr_voted',
            'nonce': this.ajaxVars.nonce,
            'post_id': post_id,
            'vote': vote
        };

        var data_str = Object.keys(data).map(function (prop) {
            return [prop, data[prop]].map(encodeURIComponent).join("=");
        }).join("&");

        this.request.open("POST", this.ajaxVars.ajaxurl, true);
        this.request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (this.responseText != null) {
                    that.ajax_response = this.responseText;
                }
                else console.log("Ajax error: No data received")
            }
        };
        this.request.send(data_str);
    }
};

var ajaxClient = new AjaxSendRequest();

var Stars = function () {
    var that = this;
    this.stars = document.querySelectorAll(".wpr-wrapp .icon-star");

    this.hasClass = function (target, className) {
        return (' ' + target.className + ' ').indexOf(' ' + className + ' ') > -1;
    };

    for (i = 0; i < this.stars.length; i++) {
        this.stars[i].addEventListener('click', function () {
            var parent = this.parentElement;
            var superparent = parent.parentElement;
            var loader = superparent.children[1];
            loader.classList.remove('wpr-hide');
            parent.classList.add('wpr-hide');
            childrens = parent.children;

            var submitStars = this.dataset.value;

            ajaxClient.ajax_vote(parent.dataset.id, submitStars);

            setTimeout(function () {
                var resp = JSON.parse(ajaxClient.ajax_response);
                for (i in childrens) {
                    if (that.hasClass(childrens[i], "checked"))
                        childrens[i].classList.remove("checked");
                }
                childrens[Math.abs(parseInt(resp['avg']) - 5)].classList.add("checked");
                document.querySelector('#wpr-widget-' + parent.dataset.id + ' .wpr-total').innerHTML = '(' + parseInt(resp['total']) + ')';
                loader.classList.add('wpr-hide');
                parent.classList.remove('wpr-hide');
            }, 500)
        });
    }
};

new Stars();
