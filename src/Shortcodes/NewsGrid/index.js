import Isotope from 'isotope-layout'
import '../../common_assets/lib/jquery-modal/jquery.modal.min'
import '../../common_assets/lib/jquery-modal/jquery.modal.min.css'
import '../../common_assets/js/modals.js'
import './index.scss'

window.jQuery.noConflict();

(function($,d) {
  'use strict';

  const prepare_modals = window.prepare_modals;

  $(d).ready(function(){

    var announcements = $(".announcements .announcements");
    if (announcements.length){
      prepare_modals($(".announcements .announcements .item:not(:first-of-type)"));
    }

    var newsGrid = $('.news-grid');
    if (newsGrid.length){
      prepare_modals($(".news-grid .item"));
      const iso = new Isotope('.news-grid',{
        itemSelector: '.category',
        percentPosition: true,
        masonry: {
          //columnWidth: '.grid-sizer'
          //gutter: unit
        }
      })
    }
  });

})(window.jQuery,document);