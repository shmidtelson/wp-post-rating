const AjaxSendRequest = function () {
    this.ajax_response = '';
    this.ajaxVars = {
        ajaxurl: "/wp-admin/admin-ajax.php",
        nonce: document.querySelector("meta[name='_wpr_nonce']").getAttribute("content")
    };

    this.request = new XMLHttpRequest();

    this.ajax_vote = function (post_id, vote) {
        const data = {
            'action': 'wpr_voted',
            'nonce': this.ajaxVars.nonce,
            'post_id': post_id,
            'vote': vote
        };

        const data_str = Object.keys(data).map(function (prop) {
            return [prop, data[prop]].map(encodeURIComponent).join("=");
        }).join("&");

        this.request.open("POST", this.ajaxVars.ajaxurl, true);
        this.request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.request.send(data_str);
    }
};

const ajaxClient = new AjaxSendRequest();

const Stars = function () {
    const that = this;
    this.stars = document.querySelectorAll(".wpr-wrapp .icon-star");

    this.hasClass = function (target, className) {
        return (' ' + target.className + ' ').indexOf(' ' + className + ' ') > -1;
    };

    for (let i = 0; i < this.stars.length; i++) {
        this.stars[i].addEventListener('click', function () {
            const parent = this.parentElement;
            const superparent = parent.parentElement;
            const loader = superparent.getElementsByClassName('wpr-rating-loader')[0];
            loader.classList.remove('wpr-hide');
            parent.classList.add('wpr-hide');
            let childrens = parent.children;

            const submitStars = this.dataset.value;

            ajaxClient.ajax_vote(parent.dataset.id, submitStars);

            ajaxClient.request.onreadystatechange = function () {
                if (ajaxClient.request.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    if (ajaxClient.request.responseText != null) {
                        setTimeout(function () {
                            try {
                                const resp = JSON.parse(ajaxClient.request.responseText);
                                for (i in childrens) {
                                    if (that.hasClass(childrens[i], "checked"))
                                        childrens[i].classList.remove("checked");
                                }

                                childrens[Math.abs(parseInt(resp.data.avg) - 5)].classList.add("checked");
                                document.querySelector('#wpr-widget-' + parent.dataset.id + ' .wpr-total').innerHTML = '(' + parseInt(resp.data.total) + ')';
                            } catch (e) {
                            }
                            loader.classList.add('wpr-hide');
                            parent.classList.remove('wpr-hide');
                        }, 300);
                    } else console.log("Ajax error: No data received")
                }
            };
        });
    }
};

new Stars();
