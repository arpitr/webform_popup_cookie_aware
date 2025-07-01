<?php

namespace Drupal\webform_popup\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Set a cookie when this webform is submitted.
 *
 * @WebformHandler(
 *   id = "webform_popup_cookie",
 *   label = @Translation("Webform Popup: Set Cookie"),
 *   category = @Translation("Custom"),
 *   description = @Translation("Sets a browser cookie when the webform is submitted."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 * )
 */
class WebformPopupCookieHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    // Only set cookie on insert, not update.
    if (!$update) {
      $webform_id = $webform_submission->getWebform()->id();
      // Set a session flag for the event subscriber.
      \Drupal::request()->getSession()->set('webform_popup_set_cookie', $webform_id);
    }
  }
}