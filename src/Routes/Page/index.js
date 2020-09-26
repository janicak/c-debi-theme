import './index.scss'

window.jQuery.noConflict();

(($,d) => {
  
  $(d).ready(function(){
    const headerImage = $('.header-image img')
    headerImage.load(function(){
      $(this).addClass('fade-in-on-load')
    })

  })

})(window.jQuery, document);