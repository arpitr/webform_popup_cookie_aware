<?php

namespace Drupal\webform_popup_cookie_aware\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sets the webform popup cookie on response if flagged in session.
 */
class WebformPopupCookieAwareCookieSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a WebformPopupCookieSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

  /**
   * Sets the cookie if the session flag is present.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event.
   */
  public function onResponse(ResponseEvent $event) {
    $request = $event->getRequest();
    $session = $request->getSession();
    if ($session->has('webform_popup_set_cookie')) {
      $webform_id = $session->get('webform_popup_set_cookie');
      $config = $this->configFactory->get('webform_popup.settings');
      $mapping = $config->get('content_type_webform_map') ?: [];

      // Find the configured expiry for this webform.
      $cookie_expiry_days = 365;
      foreach ($mapping as $type_id => $row) {
        if (isset($row['webform']) && $row['webform'] === $webform_id && !empty($row['cookie_expiry'])) {
          $cookie_expiry_days = (int) $row['cookie_expiry'];
          break;
        }
      }

      $cookie_name = 'webform_popup_submitted_' . $webform_id;
      $cookie = new Cookie(
        $cookie_name,
        '1',
        strtotime('+' . $cookie_expiry_days . ' days'),
        '/',
        NULL,
        TRUE,
        FALSE
      );
      $event->getResponse()->headers->setCookie($cookie);
      $session->remove('webform_popup_set_cookie');
    }
  }

}