<?php

namespace Drupal\webform_popup_cookie_aware\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\webform\Entity\Webform;

class WebformPopupCookieAwareController extends ControllerBase {

  public function ajaxWebform($webform_id, Request $request) {
    $webform = Webform::load($webform_id);
    if (!$webform) {
      return new JsonResponse(['error' => 'Webform not found'], 404);
    }
    $form = [
      '#type' => 'webform',
      '#webform' => $webform_id,
      '#attributes' => ['id' => 'webform-popup-form'],
    ];
    $rendered = \Drupal::service('renderer')->renderRoot($form);
    return new JsonResponse(['form' => $rendered]);
  }
}