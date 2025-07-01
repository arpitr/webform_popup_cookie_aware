<?php

namespace Drupal\webform_popup_cookie_aware\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\webform\Entity\Webform;

/**
 * Configure settings for Webform Popup Cookie Aware.
 */
class WebformPopupCookieAwareSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['webform_popup_cookie_aware.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'webform_popup_cookie_aware_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('webform_popup_cookie_aware.settings');

    // Get all node types.
    $node_types = NodeType::loadMultiple();
    $node_type_options = [];
    foreach ($node_types as $type) {
      $node_type_options[$type->id()] = $type->label();
    }

    // Get all webforms.
    $webforms = Webform::loadMultiple();
    $webform_options = [];
    foreach ($webforms as $webform) {
      $webform_options[$webform->id()] = $webform->label();
    }

    $mapping = $config->get('content_type_webform_map') ?: [];

    $form['content_type_webform_map'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Content type'),
        $this->t('Webform'),
        $this->t('Cookie expiry (days)'),
      ],
      '#empty' => $this->t('No content types found.'),
    ];

    foreach ($node_type_options as $type_id => $type_label) {
      $form['content_type_webform_map'][$type_id]['content_type'] = [
        '#markup' => $type_label,
      ];
      $form['content_type_webform_map'][$type_id]['webform'] = [
        '#type' => 'select',
        '#options' => ['' => $this->t('- None -')] + $webform_options,
        '#default_value' => isset($mapping[$type_id]['webform']) ? $mapping[$type_id]['webform'] : '',
      ];
      $form['content_type_webform_map'][$type_id]['cookie_expiry'] = [
        '#type' => 'number',
        '#min' => 1,
        '#default_value' => isset($mapping[$type_id]['cookie_expiry']) ? $mapping[$type_id]['cookie_expiry'] : 365,
        '#size' => 5,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $mapping = [];
    if ($form_state->getValue('content_type_webform_map')) {
      foreach ($form_state->getValue('content_type_webform_map') as $type_id => $row) {
        if (!empty($row['webform'])) {
          $mapping[$type_id] = [
            'webform' => $row['webform'],
            'cookie_expiry' => !empty($row['cookie_expiry']) ? (int) $row['cookie_expiry'] : 365,
          ];
        }
      }
    }
    $this->config('webform_popup_cookie_aware.settings')
      ->set('content_type_webform_map', $mapping)
      ->save();

    parent::submitForm($form, $form_state);
  }
}