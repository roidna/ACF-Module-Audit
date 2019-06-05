(function($) {
  $(document).ready(function() {
    $('#run-audit-button').on('click', function(e) {
      e.preventDefault();
      $('.acf-fcm-module-audit-results').empty();
      var $button = $(this);
      $button.data("old-title", $button.val());
      $button.val('Running');
      $button.attr('disabled', 'disabled');
      $.ajax({
        url: acfFcmModuleAudit.ajaxUrl,
        type: 'POST',
        dataType: 'html',
        data: {
          action: 'acf_fcm_module_audit_run_audit'
        },
        success: function(html) {
          $button.removeAttr('disabled');
          $button.val($button.data('old-title'));
          $('.acf-fcm-module-audit-results').hide();
          $('.acf-fcm-module-audit-results').html(html).slideDown();
        }
      });
    });
    $('body').on('click', '.acf-fcm-module-audit-results ul.groups > li > h3', function(e) {
      $(this).parent().find('ul.fields').slideToggle();
    });

    $('body').on('click', '.acf-fcm-module-audit-results ul.groups > li > ul.fields li h4', function(e) {
      $(this).parent().find('ul.modules').slideToggle();
    });

    $('body').on('click', '.acf-fcm-module-audit-results ul.groups > li > ul.fields li > ul.modules h5', function(e) {
      $(this).parent().find('ul.urls').slideToggle();
    });


  });
})(jQuery);
