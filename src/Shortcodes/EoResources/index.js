import Isotope from 'isotope-layout'
import './index.scss'

window.jQuery.noConflict();

(function($,d) {
  'use strict';

  $(d).ready(function(){

    var eoResources = $('.eo-resources');
    if (eoResources.length) {
      prepare_eo_resources(eoResources);
    }

  });

  function prepare_eo_resources(eoResources){
    var grid = eoResources.find('.c-debi-accordion');
    var selects = eoResources.find('select');

    //grid.imagesLoaded(function(){
    // Init Isotope Grid
    grid.isotope = new Isotope('.c-debi-accordion', {
      itemSelector: '.section',
      layoutMode: 'fitRows'
    });
    // On select change
    selects.on('change', function(){
      // Get current select values
      var filters = {};
      selects.each(function(){
        var select = $(this);
        filters[select.attr('data-filter')] = select.val();
      });
      // Filter items
      grid.isotope.arrange({ filter: function(){
          var item = $(this);
          var selected = true;
          Object.keys(filters).forEach(function(key){
            if (selected){
              var value = filters[key];
              if (value != '*'){
                var itemAtts = item.attr('data-'+key).split(/\s+/);
                if ($.inArray(value, itemAtts) == -1){
                  selected = false;
                }
              }
            }
          });
          return selected;
        }});

      // Disable select options on other select as needed
      var allItems = grid.isotope.getItemElements();
      var changedFilter = $(this).attr('data-filter');
      var changedFilterValue = filters[changedFilter];
      var otherItemAttrs = [];
      allItems.forEach(function(item){
        item = $(item);
        var itemFilterAttrs = item.attr('data-'+changedFilter).split(/\s+/);
        if ($.inArray(changedFilterValue, itemFilterAttrs) != -1){
          Object.keys(filters).forEach(function(key){
            if (key != changedFilter){
              var itemOtherAttrs = item.attr('data-'+key).split(/\s+/);
              itemOtherAttrs.forEach(function(itemAttr){
                if ($.inArray(itemAttr, otherItemAttrs) == -1){
                  otherItemAttrs.push(itemAttr);
                }
              });
            }
          });
        }
      });
      selects.each(function(){
        var select = $(this);
        var filter = select.attr('data-filter');
        if (filter != changedFilter){
          select.children('option').each(function(){
            var option = $(this);
            var disabled = false;
            if (changedFilterValue != '*'){
              var value = option.attr('value');
              disabled = $.inArray(value, otherItemAttrs) == -1;
              disabled = value == '*' ? false : disabled;
            }
            option.attr('disabled', disabled);
          });
        }
      })
    });
    // On detail click
    grid.find('.section-title .details').on('click', function(){
      grid.isotope.layout();
      $(this).closest('.section').children('.section-content').toggleClass('ready');
    });
    //});
  }

})(window.jQuery,document);