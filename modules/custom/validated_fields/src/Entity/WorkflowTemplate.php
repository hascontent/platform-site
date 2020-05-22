<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Workflow template entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "workflow_template",
 *   label = @Translation("Workflow template"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\WorkflowTemplateListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\WorkflowTemplateViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\WorkflowTemplateForm",
 *       "add" = "Drupal\validated_fields\Form\WorkflowTemplateForm",
 *       "edit" = "Drupal\validated_fields\Form\WorkflowTemplateForm",
 *       "delete" = "Drupal\validated_fields\Form\WorkflowTemplateDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\validated_fields\WorkflowTemplateHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\WorkflowTemplateAccessControlHandler",
 *   },
 *   base_table = "workflow_template",
 *   translatable = FALSE,
 *   admin_permission = "administer workflow template entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/workflow_template/{workflow_template}",
 *     "add-form" = "/admin/structure/workflow_template/add",
 *     "edit-form" = "/admin/structure/workflow_template/{workflow_template}/edit",
 *     "delete-form" = "/admin/structure/workflow_template/{workflow_template}/delete",
 *     "collection" = "/admin/structure/workflow_template",
 *   },
 *   field_ui_base_route = "workflow_template.settings"
 * )
 */
class WorkflowTemplate extends ContentEntityBase implements WorkflowTemplateInterface {

  use EntityChangedTrait;
  // use EntityPublishedTrait;

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getName() {
  //   return $this->get('name')->value;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function setName($name) {
  //   $this->set('name', $name);
  //   return $this;
  // }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    // $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Workflow template entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // $fields['status']->setDescription(t('A boolean indicating whether the Workflow template is published.'))
    //   ->setDisplayOptions('form', [
    //     'type' => 'boolean_checkbox',
    //     'weight' => -3,
    //   ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));
      
    $fields['stage_fields'] = BasefieldDefinition::create('entity_reference')
      ->setLabel(t('Validated Fields'))
      ->setDescription(t('The Validated Field Entities'))
      ->setSetting('target_type','validated_field_type')
      ->setSetting('handler','default')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setCardinality(-1); //infinite cardinality
    return $fields;
  }

}
