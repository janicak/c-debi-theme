import '../../common_assets/lib/slick/slick.min'
import './index.scss'

window.jQuery.noConflict();

(function($,d) {
  $(d).ready(function() {

    var slider = $('.cdebi-slider');
    if ( slider.length ) {
      slider.on('init', function(slick){
        $('#loader').remove();
      });
      slider.slick({
        lazyLoad: 'ondemand',
        fade: true,
        cssEase: 'linear',
        //autoplay: true,
        adaptiveHeight: false,
        autoplaySpeed: 6000,
        dots: true,
        focusOnSelect: false,
        responsive: [
          {
            breakpoint: 970,
            settings: {
              adaptiveHeight: true
            }
          }
        ]
      });
    }
  });

})(window.jQuery,document);