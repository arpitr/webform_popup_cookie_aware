<?php
namespace Drupal\webform_popup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\webform\Entity\Webform;

/**
 * Provides a 'Webform Popup' block.
 *
 * @Block(
 *   id = "webform_popup_block",
 *   admin_label = @Translation("Webform Popup Block"),
 * )
 */
class WebformPopupBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new WebformPopupBlock instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
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

    $config = \Drupal::config('webform_popup.settings');
    $enabled_types = $config->get('enabled_node_types') ?: [];
    if (empty($enabled_types[$node->bundle()])) {
      return [];
    }

    $webform_id = $config->get('webform_id') ?: 'contact';
    $webform = Webform::load($webform_id);
    if (!$webform) {
      return [];
    }
      // Build the popup markup with the webform as a child.
    return [
      '#prefix' => '<div id="webform-popup-overlay" class="webform-popup-overlay" style="display:none;"><div class="webform-popup-content"><button id="webform-popup-close" class="webform-popup-close" type="button">&times;</button>',
      '#suffix' => '</div></div>',
      'form' => [
        '#type' => 'webform',
        '#webform' => $webform_id,
        '#attributes' => ['id' => 'webform-popup-form'],
      ],
      '#attached' => [
        'library' => [
          'webform_popup/popup',
        ],
      ],
    ];
  }
}