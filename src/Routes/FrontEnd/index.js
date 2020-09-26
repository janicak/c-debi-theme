import './index.scss'

window.jQuery.noConflict();

(function($,d) {

  window.prepare_accordions = prepare_accordions

  $(d).ready(function(){
    var accordions = $('.c-debi-accordion');
    prepare_accordions(accordions);
  })

  function prepare_accordions(accordions){
    accordions.find('.section-title .details').on('click', function(){
      var title = $(this);
      var accordion = title.closest('.c-debi-accordion');
      var section = title.closest('.section');
      if (section.attr('class').split(/\s+/).indexOf('active') != -1) {
        accordion.find('.section').removeClass('active');
      } else {
        accordion.find('.section').removeClass('active');
        title.closest('.section').addClass('active');
      }
    })
  }

})(window.jQuery,document);

