<?php

namespace Drupal\webform_popup_cookie_aware\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\webform\Entity\Webform;

/**
 * Provides a 'Webform Popup Cookie Aware' block.
 *
 * @Block(
 *   id = "webform_popup_cookie_aware_block",
 *   admin_label = @Translation("Webform Popup Cookie Aware Block"),
 * )
 */
class WebformPopupCookieAwareBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new WebformPopupCookieAwareBlock instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');
    if (!$node) {
      return [];
    }

    $config = $this->configFactory->get('webform_popup_cookie_aware.settings');
    $mapping = $config->get('content_type_webform_map') ?: [];
    $bundle = $node->bundle();

    if (empty($mapping[$bundle]['webform'])) {
      return [];
    }

    $webform_id = $mapping[$bundle]['webform'];
    $cookie_expiry = !empty($mapping[$bundle]['cookie_expiry']) ? (int) $mapping[$bundle]['cookie_expiry'] : 365;

    $webform = Webform::load($webform_id);
    if (!$webform) {
      return [];
    }

    $cookie_name = 'webform_popup_submitted_' . $webform_id;

    return [
      '#prefix' => '<div id="webform-popup-overlay" class="webform-popup-overlay" style="display:none;" data-webform-id="' . $webform_id . '" data-cookie-name="' . $cookie_name . '" data-cookie-expiry="' . $cookie_expiry . '"><div class="webform-popup-content"><button id="webform-popup-close" class="webform-popup-close" type="button">&times;</button>',
      '#suffix' => '</div></div>',
      'placeholder' => [
        '#markup' => '<div id="webform-popup-form-placeholder"></div>',
      ],
      '#attached' => [
        'library' => [
          'webform_popup_cookie_aware/popup',
        ],
      ],
      '#cache' => [
        'contexts' => ['url'],
      ],
      '#webform' => $webform_id,
      '#attributes' => ['id' => 'webform-popup-form'],
    ];
  }

}