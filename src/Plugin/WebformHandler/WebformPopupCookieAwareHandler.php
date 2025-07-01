<?php

namespace Drupal\webform_popup_cookie_aware\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Set a cookie when this webform is submitted.
 *
 * @WebformHandler(
 *   id = "webform_popup_cookie_aware",
 *   label = @Translation("Webform Popup Cookie Aware: Set Cookie"),
 *   category = @Translation("Custom"),
 *   description = @Translation("Sets a browser cookie when the webform is submitted."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 * )
 */
class WebformPopupCookieAwareHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->t('Set session key webform_popup_set_cookie. Session is further used in Event Susbcriber to set the key.');
  }

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