import '../sass/main.scss'
import '@romua1d/star-rating-js/build/index.css'

import StarRating from '@romua1d/star-rating-js';


const AjaxSendRequest = function () {
  this.ajax_response = '';
  this.ajaxVars = {
    ajaxurl: "/wp-admin/admin-ajax.php",
    nonce: document.querySelector("meta[name='_wpr_nonce']").getAttribute("content")
  };

  this.request = new XMLHttpRequest();

  this.ajax_vote = function (post_id, vote, instance) {
    const data = {
      'action': 'wpr_voted',
      'nonce': this.ajaxVars.nonce,
      'post_id': post_id,
      'vote': vote
    };

    const data_str = Object.keys(data).map(function (prop) {
      return [prop, data[prop]].map(encodeURIComponent).join("=");
    }).join("&");

    instance.changeLoader(true);
    this.request.open("POST", this.ajaxVars.ajaxurl, true);
    this.request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    this.request.send(data_str);
  }
};

const ajaxClient = new AjaxSendRequest();

const stars = document.querySelectorAll(".wpr-wrapp");

for (let i = 0; i < stars.length; i++) {
  const {total, value, votestitle, id, fontsize, starscolor, textcolor, backgroundcolor} = stars[i].dataset
  const message = votestitle + ' (' + total + ')';
  const StarRatingInstance = new StarRating(stars[i], {
    message,
    value: parseInt(value) ? value : 1,
    size: fontsize,
    starsColor: starscolor,
    textColor: textcolor,
    infoPanelBackgroundColor: backgroundcolor,
  });

  StarRatingInstance.onChange = (e) => {
    if (!e?.target?.dataset?.value) {
      return;
    }

    ajaxClient.ajax_vote(id, e.target.dataset.value, StarRatingInstance)

    ajaxClient.request.onreadystatechange = function () {
      if (ajaxClient.request.readyState === XMLHttpRequest.DONE && this.status === 200) {
        if (ajaxClient.request.responseText != null) {
          setTimeout(function () {
            try {
              const resp = JSON.parse(ajaxClient.request.responseText);

              StarRatingInstance.changeRatingValue(parseInt(resp.data.avg));
              StarRatingInstance.changeMessage(votestitle + ' (' + resp.data.total + ')')
              StarRatingInstance.changeLoader(false)
            } catch (e) {
              StarRatingInstance.changeLoader(false)
            }
          }, 300);
        } else{
          console.log("Ajax error: No data received")
          StarRatingInstance.changeLoader(false)
        }
      }
    };
  }
}
