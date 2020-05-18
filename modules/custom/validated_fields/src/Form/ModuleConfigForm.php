<?php

namespace Drupal\validated_fields\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'your_module_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'your_module.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('validated_fields.settings');
    $form['weekend_interval'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Do Intervals Skip Weekends'),
      '#default_value' => $config->get('interval_skip_weekends'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // $this->config('validated_fields.settings')
    //   ->set('interval_skip_weekends', $form_state->getValue('weekend_interval'))
    //   ->save();
    \Drupal::configFactory()->getEditable('validated_fields.settings')
      ->set('interval_skip_weekends', $form_state->getValue('weekend_interval'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}