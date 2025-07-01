/**
 * @file
 * Webform Popup behavior for showing/hiding the popup based on a cookie.
 */

(function ($, Drupal, once) {
  'use strict';

  /**
   * Webform Popup behavior.
   */
  Drupal.behaviors.webformPopup = {
    attach: function (context, settings) {
      once('webform-popup', '#webform-popup-overlay', context).forEach(function (element) {
        var $popup = $(element);
        $popup.hide();
        var cookieName = $popup.data('cookie-name') || 'webform_popup_submitted';

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

        console.log('Webform Popup: Cookie not found, showing popup.', cookieName);    
        console.log(getCookie(cookieName)); 
        if (!getCookie(cookieName)) {   
          $popup.show();
        }

        $popup.find('#webform-popup-close').on('click', function () {
          $popup.hide();
        });
      });
    }
  };

})(jQuery, Drupal, once);