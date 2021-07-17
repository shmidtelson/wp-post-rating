import '../sass/admin.scss'
import '@romua1d/star-rating-js/build/index.css'

import StarRating from '@romua1d/star-rating-js';

jQuery(document).ready(function ($) {
  const StarRatingInstance = new StarRating(document.querySelectorAll(".wpr-wrapp")[0], {
    message: '(5)',
    value: 4,
    size: '30px',
  });

  const mainColor = $('.color_chooser_js');
  const textColor = $('.text_color_chooser_js');
  const textBackgroundColor = $('.text_background_color_chooser_js');

  const setCssVariables = function () {
    StarRatingInstance.changeColor(mainColor.val());
    // document.documentElement.style.setProperty('--wpr-main-color', );
    // document.documentElement.style.setProperty('--wpr-text-color', textColor.val());
    // document.documentElement.style.setProperty('--wpr-text-background-color', textBackgroundColor.val());
  };

  setCssVariables()

  let myOptions = {
    change: function () {
      setCssVariables()
    },
  };
  mainColor.wpColorPicker(myOptions);
  textColor.wpColorPicker(myOptions);
  textBackgroundColor.wpColorPicker(myOptions);
});
