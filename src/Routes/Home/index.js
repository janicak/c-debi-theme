import "./index.scss"
import waveClipSvg from '../../common_assets/img/wave-clip.svg'

window.jQuery.noConflict();

(($,d) => {

  var mounds = $('.mounds .mounds');
  if (mounds) {
    prepare_mounds(mounds);
  }

  function prepare_mounds(mounds) {
    $.get(waveClipSvg, function(data) {
      var svgElem = $(data.activeElement);
      mounds.append(svgElem);
      $('.mounds .mounds').attr('style', 'clip-path: url(#mounds-clip)');
      var width = $(window).width();
      var scale = width / 100;
      $('#mounds-clip').attr('transform', 'scale('+scale+', 1)');
      $(window).on('resize orientationChange', function(event) {
        var width = $(window).width();
        var scale = width / 100;
        $('#mounds-clip').attr('transform', 'scale('+scale+', 1)');
      });
    });
  }

})(window.jQuery, document);