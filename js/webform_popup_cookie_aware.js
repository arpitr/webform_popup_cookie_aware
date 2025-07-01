/**
 * @file
 * Webform Popup behavior for showing/hiding the popup based on a cookie.
 */

(function ($, Drupal, once) {
  'use strict';

  /**
   * Webform Popup behavior.
   */
  Drupal.behaviors.webformPopupCookieAwarewebformPopup = {
    attach: function (context, settings) {
      console.log('Webform Popup Cookie Aware behavior attached.'); 
      once('webform-popup', '#webform-popup-overlay', context).forEach(function (element) {
        var $popup = $(element);
        $popup.hide();
        var cookieName = $popup.data('cookie-name') || 'webform_popup_submitted';
        var webformId = $popup.data('webform-id');
        var $placeholder = $('#webform-popup-form-placeholder', $popup);

        /**
         * Get a cookie value by name.
         *
         * @param {string} name
         *   The name of the cookie.
         *
         * @return {string|null}
         *   The cookie value or null if not found.
         */
        function getCookie(name) {
          var value = '; ' + document.cookie;
          var parts = value.split('; ' + name + '=');
          if (parts.length === 2) {
            return parts.pop().split(';').shift();
          }
          return null;
        }

        function loadWebform() {
          if ($placeholder.children().length === 0 && webformId) {
            $.get(Drupal.url('webform-popup-cookie-aware/ajax/' + webformId), function (data) {
              if (data.form) {
                $placeholder.html(data.form);
                // Re-attach behaviors for the loaded form.
                Drupal.attachBehaviors($placeholder[0]);
              }
            });
          }
        }

        console.log('Webform Popup: Cookie not found, showing popup.', cookieName);    
        console.log(getCookie(cookieName)); 
        if (!getCookie(cookieName)) {   
          $popup.show();
          loadWebform();
        }

        $popup.find('#webform-popup-close').on('click', function () {
          $popup.hide();
        });
      });
    }
  };

})(jQuery, Drupal, once);