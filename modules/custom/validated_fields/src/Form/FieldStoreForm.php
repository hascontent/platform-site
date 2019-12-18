<?php

namespace Drupal\validated_fields\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the field_store entity edit forms.
 *
 * @ingroup validated_fields
 */
class FieldStoreForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\validated_fields\Entity\FieldStore */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    // $form['langcode'] = [
    //   '#title' => $this->t('Language'),
    //   '#type' => 'language_select',
    //   '#default_value' => $entity->getUntranslated()->language()->getId(),
    //   '#languages' => Language::STATE_ALL,
    // ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.field_store.collection');
    $entity = $this->getEntity();
    $entity->save();
  }

}
?>
