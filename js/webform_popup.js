(function ($, Drupal) {
  Drupal.behaviors.webformPopup = {
    attach: function (context, settings) {
      if (window.webformPopupInitialized) return;
      window.webformPopupInitialized = true;

      function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
      }
      function setCookie(name, value, days) {
        let expires = "";
        if (days) {
          let date = new Date();
          date.setTime(date.getTime() + (days*24*60*60*1000));
          expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
      }

      if (!getCookie('webform_popup_submitted')) {
        $('#webform-popup-overlay').show();
      }

      $('#webform-popup-close').on('click', function () {
        $('#webform-popup-overlay').hide();
      });

      $('#webform-popup-form').on('submit', function () {
        setCookie('webform_popup_submitted', '1', 365);
        $('#webform-popup-overlay').hide();
      });
    }
  };
})(jQuery, Drupal);