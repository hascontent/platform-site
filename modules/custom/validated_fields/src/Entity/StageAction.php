<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Stage action entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "stage_action",
 *   label = @Translation("Stage action"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\StageActionListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\StageActionViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\StageActionForm",
 *       "add" = "Drupal\validated_fields\Form\StageActionForm",
 *       "edit" = "Drupal\validated_fields\Form\StageActionForm",
 *       "delete" = "Drupal\validated_fields\Form\StageActionDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\validated_fields\StageActionHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\StageActionAccessControlHandler",
 *   },
 *   base_table = "stage_action",
 *   translatable = FALSE,
 *   admin_permission = "administer stage action entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/stage_action/{stage_action}",
 *     "add-form" = "/admin/structure/stage_action/add",
 *     "edit-form" = "/admin/structure/stage_action/{stage_action}/edit",
 *     "delete-form" = "/admin/structure/stage_action/{stage_action}/delete",
 *     "collection" = "/admin/structure/stage_action",
 *   },
 *   field_ui_base_route = "stage_action.settings"
 * )
 */
class StageAction extends ContentEntityBase implements StageActionInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

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
      ->setDescription(t('The name of the Stage action entity.'))
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields["next_stage"] = BaseFieldDefinition::create('entity_reference')
    ->setSetting('target_type','stage')
    ->setSetting('handler','default')
    ->setCardinality(1);

    return $fields;
  }
}