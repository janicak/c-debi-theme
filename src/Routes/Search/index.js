import Isotope from 'isotope-layout'
import '../../common_assets/js/modals.js'

//Importing search css for all front-end pages
//import './index.scss'

window.jQuery.noConflict();

(function($,d) {
  $(d).ready(function() {

    var searchForm = $('form#search-header');
    if (searchForm.length){
      prepareSearchForm(searchForm);
      monkeyPatchPaginationBug();
    }

    var grid = $('.grid-list');
    if (grid.length){
      prepareGrid(grid);
      var filters = $('.filters');
      if (filters.length){
        prepareFilters(filters, grid);
      }
      var sorts = $('.sort-item');
      if (sorts.length){
        prepareSorts(sorts, grid);
      }
    }

  });

  function monkeyPatchPaginationBug(){
    var bugLink = $('.nav-links').children('a').attr('href', '*|UPDATE_PROFILE|*').eq(0);
    if (bugLink.length){
      bugLink.replaceWith(bugLink.html())
    }
  }

  function prepareSearchForm(searchForm){
    var postTypeSelect = $('#post-type-select');
    if (postTypeSelect.length){
      update_selects(postTypeSelect, true);
    }
    searchForm.on('submit', function(){
      var queryField = $($(this).find('input[name="s"]')[0]);
      var query = queryField.val();
      queryField.attr('value', query);
      var selected = false;
      $(this).find('select').each(function(){
        if (!$(this).val()){
          $(this).attr("disabled", "disabled");
        } else {
          selected = true;
        }
      });
      if (!query && !selected){
        $(this).append('<span class="error">Please enter a query term or select a type</span>');
        return false;
      }
      return true;
    });
  }

  function update_selects(postTypeSelect, init){
    if (init) {
      postTypeSelect.on('change', function () {
        update_selects($(this), false);
      });
    }
    var postType = $(postTypeSelect.children('select')[0]).val();
    var pubTypeSelect = $('#publication-type-select');
    var awardTypeSelect = $('#award-type-select');
    var postCatSelect = $('#post-category-select');
    if (postType == 'publication') {
      update_options(pubTypeSelect, [awardTypeSelect, postCatSelect], init, 'publication_type');
    } else if (postType == 'award'){
      update_options(awardTypeSelect, [pubTypeSelect, postCatSelect], init, 'award_type');
    } else if (postType == 'post') {
      update_options(postCatSelect, [pubTypeSelect, awardTypeSelect], init, 'category');
    } else {
      update_options(postTypeSelect, [postCatSelect, pubTypeSelect, awardTypeSelect], init, null, null);
    }
  }

  function update_options(active_select, inactive_selects, init, extraParam){
    $.each(inactive_selects, function(){
      var select = $(this);
      select.addClass('hidden');
      select.find('option').each(function(i, el){
        $(el).prop('selected', i == 0);
      });
      $(select.find('option')[0]).prop('selected', true);
    });
    if (init){
      var subType = '';
      if (extraParam){
        subType = $_GET(extraParam);
      }
      if (!subType){
        var pathParts = window.location.pathname.split('/');
        for (var i = 0; i < pathParts.length; i++){
          if (pathParts[i] == 'category') { subType = pathParts[i+1]; }
        }
      }
      active_select.find('option').each(function(i, el){
        if (subType){
          var name = $(el).val();
          $(el).prop('selected', name == subType);
        }
      });
    }
    if (active_select){
      active_select.removeClass('hidden');
    }
  }

  function prepareFilters(filters, grid){
    if (filters.children().length > 0){
      filters.removeClass('hidden');
      $('.filters-list.post-types').removeClass('hidden');
      if (filters.children().length == 1) {
        filters.children().removeClass('hidden');
      }
      // Initial filter update without selector
      update_filters(null, true);
    }
    var params = $_GET();
    if (params['s']){
      if (params['publication_type'] || params['award_type']){
        filters.addClass('hidden');
      }
    }
    $('.filters-item').click(function(e){
      var button = $(e.target);
      if (button.hasClass('active') || button.hasClass('disabled')) {
        return;
      }
      var filterParent = $(button.parentsUntil('.data-filters'));
      var selector = button.attr('data-filter');

      grid.isotope.arrange({filter: function(){
          var filters = $(this).attr('class').split(/\s+/);
          var expiredInput = $('input[name="show-expired"]');
          if (expiredInput.length){
            var showExpired = expiredInput[0].checked;
            if ($.inArray('expired', filters) != -1 && !showExpired){
              return false;
            }
          }
          if (selector == '*') {
            if (filterParent.hasClass('post-types')) {
              return true;
            } else if (filterParent.hasClass('publication-types')){
              var inArray = $.inArray('publication', filters);
              return inArray != -1;
            } else if (filterParent.hasClass('award-types')){
              inArray = $.inArray('award', filters);
              return inArray != -1;
            }  else if (filterParent.hasClass('post-cats')){
              inArray = $.inArray('post', filters);
              return inArray != -1;
            }
          }
          inArray = $.inArray(selector, filters);
          return inArray != -1;
        }});
      update_filters(button);
    });
    var expiredFilter = $('.filters-item[data-filter="expired"]');
    expiredFilter.addClass('disabled');
    $('.show-expired input').change(function(e){
      var selector = $($('.filters-item.active')[0]).attr('data-filter');
      expiredFilter.toggleClass('disabled');
      var input = $(this);
      grid.isotope.arrange({filter: function(){
          if (input.is(':checked')){
            if (selector == '*'){
              return true;
            } else  {
              filters = $(this).attr('class').split(/\s+/);
              return $.inArray(selector, filters) != -1;
            }
          } else {
            var expired = $(this).hasClass('expired');
            if (selector == '*'){
              if (!expired){
                return true;
              }
            } else  {
              filters = $(this).attr('class').split(' ');
              var inArray = $.inArray(selector, filters) != -1;
              if (inArray && !expired){
                return true;
              }
            }
          }
        }});
    });
  }

  function update_filters(button, init){
    var subtypes = {
      publication: $('.filters-list.publication-types'),
      award: $('.filters-list.award-types'),
      post:$('.filters-list.post-cats')
    };

    // If this is the intial call, hide sub-types
    if (init){
      if ($('.filters').children().length != 1){
        $('.filters-list:not(.post-types)').addClass('hidden');
      }
    } else {
      // If button is post type, display children, hide others
      if (button.closest('.filters-list').hasClass('post-types')){
        $('.filters-list .filters-item').removeClass('active');
        button.addClass('active');
        $('.filters-list:not(.post-types)').addClass('hidden');
        var dataFilter = button.attr('data-filter');
        if (subtypes.hasOwnProperty(dataFilter)){
          subtypes[dataFilter].removeClass('hidden');
        }

        // If button is sub-type, keep post type selected
      } else {
        $('.filters-list:not(.post-types) .filters-item').removeClass('active');
        button.addClass('active');
      }

    }
  }

  function prepareSorts(sorts, grid){
    sorts.click(function(e){
      var button = $(e.target);
      if (button.hasClass('active')) {
        return;
      }
      var selector = button.attr('data-sort');
      if (selector == 'asc'){
        grid.isotope.arrange({ sortBy: 'date', sortAscending: true });
      } else if (selector == 'desc') {
        grid.isotope.arrange({ sortBy: 'date', sortAscending: false });
      } else if (selector == 'search') {
        grid.isotope.arrange({ sortBy: 'searchOrder' });
      } else if (selector == 'title-desc'){
        grid.isotope.arrange({ sortBy: 'title', sortAscending: false });
      } else if (selector == 'title-asc'){
        grid.isotope.arrange({ sortBy: 'title', sortAscending: true });
      }
      $('.sort-item').removeClass('active');
      button.addClass('active');
    });
  }

  function prepareGrid(grid){
    $('.grid-list > article.search-result').each(function(i, el){
      $(el).attr('data-search-order', i);
    });
    window.prepare_modals($('a[rel="modal"]'));
    $('#loader').remove();
    grid.removeClass('hidden');

    //grid.imagesLoaded(function(){
    var initialSort = $('.sort-item.active').eq(0).attr('data-sort');
    var sortBy = '';
    var sortAscending = false;
    if (initialSort == 'asc'){
      sortBy = 'date';
      sortAscending = true;
    } else if (initialSort == 'desc') {
      sortBy = 'date';
    } else if (initialSort == 'search') {
      sortBy = 'searchOrder';
      sortAscending = true;
    } else if (initialSort == 'title-desc'){
      sortBy = 'title';
    } else if (initialSort == 'title-asc'){
      sortBy = 'title';
      sortAscending = true;
    }
    grid.isotope = new Isotope('.grid-list',{
      itemSelector: '.grid-list >  article.search-result',
      layoutMode: 'fitRows',
      getSortData: {
        date: function(itemEl){
          return $(itemEl).attr('data-sort');
        },
        searchOrder: function(itemEl){
          return $(itemEl).attr('data-search-order');
        },
        title: function(itemEl){
          return $(itemEl).find('.title').eq(0).text();
        }
      },
      filter: function(){
        return !$(this).hasClass('expired');
      },
      sortBy: sortBy,
      sortAscending: sortAscending
    });
    //});
  }

  function $_GET(param) {
    var vars = {};
    window.location.href.replace( location.hash, '' ).replace(
      /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
      function( m, key, value ) { // callback
        vars[key] = value !== undefined ? value : '';
      }
    );

    if ( param ) {
      return vars[param] ? vars[param] : null;
    }
    return vars;
  }

})(window.jQuery,document)