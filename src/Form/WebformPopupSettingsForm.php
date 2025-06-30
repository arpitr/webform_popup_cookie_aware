<?php

namespace Drupal\webform_popup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Configure settings for Webform Popup.
 */
class WebformPopupSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['webform_popup.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'webform_popup_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('webform_popup.settings');

    // Get all node types.
    $node_types = NodeType::loadMultiple();
    $options = [];
    foreach ($node_types as $type) {
      $options[$type->id()] = $type->label();
    }

    $form['enabled_node_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Show popup on these content types'),
      '#options' => $options,
      '#default_value' => $config->get('enabled_node_types') ?: [],
      '#description' => $this->t('Select the content types where the popup should appear.'),
    ];

    $form['webform_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webform ID'),
      '#default_value' => $config->get('webform_id') ?: 'contact',
      '#description' => $this->t('Enter the machine name of the webform to show in the popup.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('webform_popup.settings')
      ->set('enabled_node_types', array_filter($form_state->getValue('enabled_node_types')))
      ->set('webform_id', $form_state->getValue('webform_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}