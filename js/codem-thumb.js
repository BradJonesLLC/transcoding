(function ($) {

  Drupal.behaviors.codemThumb = {
    attach: function (context, settings) {
      maxSlide = parseFloat($('#slider').attr('data-duration'));
      $('#slider', context).slider({
        min: 0,
        max: maxSlide,
        slide: function (e, ui) {
          $('input[name=thumb_select]').val(ui.value);
        },
        change: function (e, ui) {
          $('input[name=thumb_select]').val(ui.value);
          $('#thumb-throbber').show();
          $('.form-item-thumb-select label').addClass('throbber');
          nodeId = $('#slider').attr('data-node');
          $.ajax({
            url: Drupal.settings.basePath + 'codem/thumb/' + nodeId + '/' + ui.value,
            success: function (data) {
              //console.log($.parseJSON(data));
              data = $.parseJSON(data);
              $('#thumb-preview').html('<img src="' + data.path + '" />');
              $('#thumb-throbber').hide();
            },
          });
        },
      });
      $('#thumb-strip', context).click(function (e) {
        $('#slider').slider('value', $(e.target).attr('data-frame'));
      });
      $('#slider').slider('value', $('input[name=thumb_select]').first().val());
    }
  };

})(jQuery);
