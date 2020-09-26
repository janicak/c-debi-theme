import '../lib/jquery-modal/jquery.modal.min'
import '../lib/jquery-modal/jquery.modal.min.css'

window.jQuery.noConflict();

(function($,d) {

  window.prepare_modals = prepare_modals;

  if ($.modal) {
    $.modal.defaults.closeExisting = true;
    $.modal.defaults.clickClose = false;
  }

  function prepare_modals(selector){
    selector.click(function(e) {
      e.preventDefault();
      var url = $(this).attr('data-url');
      if (url){
        window.location.href = url;
      } else {
        // GET MODAL HTML
        var current = $('.post[data-id="'+$(this).attr('data-id')+'"]').eq(0);
        var modalContent = current.html();
        var modal = $("<div class='modal'><div class='modal-container'>"+modalContent+"</div></div>");

        // ADD CONTEXTUAL STYLING TO MODAL
        $('html').attr('style', 'margin-right:15px !important;');
        color = $(this).closest('article').css('color');
        if (!color || color == 'rgb(0, 0, 0)'){
          var color = $(this).css('color');
        }
        var gradient = 'linear-gradient(to bottom, '+color+' 0%, #6f8a72 100%)';
        $(document).on($.modal.BLOCK, function(e, modal){
          setTimeout(function(){
            $(modal.elm).css({background: gradient });
            modal.$blocker.addClass('ready');
          }, 1);
        });

        // APPEND MODAL
        modal.appendTo('body').modal({
          closeClass: 'icon-remove',
          closeText: '<span class="x">×</span>'
        });

        // SETUP FIXED POSITIONING FOR MODAL CONTROLS
        modalFixedPositions($('.blocker .modal'));
        $(window).on('resize', function(){
          modalFixedPositions($('.blocker .modal'));
        });

        // ADD CUSTOM MODAL CLOSE BEHAVIOR
        $('.modal').on('click', function(e){
          e.stopPropagation();
        });
        $('.arrow').on('click', function(e){
          e.stopPropagation();
        });
        close_modal($('.blocker'));
        close_modal($('a[rel="modal:close"]'));

        // PREVENT NEW MODAL WHEN CLICKING ON EMBEDDED ITEM AND GO DIRECTLY TO URL
        modal.find('.item').on('click', function(){
          var item = $(this);
          var url = item.find('.title a').eq(0).attr('href');
          if (url) {
            window.location.href = url;
          }
        });

        var accordions = modal.find('.c-debi-accordion');
        prepare_accordions(accordions);

      }
    });
  }

  function modalFixedPositions(modal){
    var windowHeight = $(window).height();
    var windowWidth = $(window).width();
    var top = 'calc('+windowHeight+'px / 2 - 2.5rem)';
    var visibility = 'visible';
    if (windowWidth > 1080){
      var leftMargin = 'calc(('+windowWidth+'px - 55rem) /2 - 5rem - 15px)';
      var rightMargin = 'calc(('+windowWidth+'px - 55rem) /2 - 5rem)';
      var topMargin = '1rem';
    } else {
      leftMargin = '1rem';
      rightMargin = '1rem';
    }
    if (windowWidth < 890) {
      visibility = 'hidden';
    }
    var scrollVisible = modal.eq(0).height() > windowHeight;
    if (!scrollVisible) {
      modal.attr('style', 'right: 7.5px');
    }
    var arrowPrev = modal.find('.arrow.previous');
    if (arrowPrev.length){
      arrowPrev.css({
        top: top,
        left: leftMargin,
        visibility: visibility
      });
      arrowPrev[0].style.webkitTransform = 'translateZ('+top+')';
    }
    var arrowNext = modal.find('.arrow.next');
    if (arrowNext.length){
      arrowNext.css({
        top: top,
        right: rightMargin,
        visibility: visibility
      });
      arrowNext[0].style.webkitTransform = 'translateZ('+top+')';
    }
    var closeButton = modal.find('a.close-modal');
    if (closeButton.length){
      closeButton.css({
        right: rightMargin,
        top: topMargin
      });
      closeButton[0].style.webkitTransform = 'translateZ('+topMargin+')';
    }
  }

  function close_modal(selector) {
    selector.on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      setTimeout(function() {
        var modal = $.modal.getCurrent();
        modal.$blocker.removeClass('ready');
        setTimeout(function(){
          $.modal.close();
          $('html').attr('style', '');
        }, 400);
      }, 1);
    });
  }

})(window.jQuery,document);