<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Stage field permission entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "stage_field_permission",
 *   label = @Translation("Stage field permission"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\StageFieldPermissionListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\StageFieldPermissionViewsData",
 *
 *     "access" = "Drupal\validated_fields\StageFieldPermissionAccessControlHandler",
 *   },
 *   base_table = "stage_field_permission",
 *   translatable = FALSE,
 *   admin_permission = "administer stage field permission entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 * )
 */
class StageFieldPermission extends ContentEntityBase implements StageFieldPermissionInterface {

  use EntityChangedTrait;

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

    $fields['permission_level'] = BaseFieldDefinition::create('integer')
      ->setLabel(t("Permission Level"))
      ->setDescription(t("Sets amount of permissions owner has"))
      ->addPropertyConstraints('value',['Range'=> ['min' => 0, 'max' => 3]])
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'number'
      ]);

    $fields['workflow_field'] = baseFieldDefinition::create('entity_reference')
      ->setLabel(t('Workflow Field'))
      ->setSetting('target_type','validated_field_type')
      ->setSetting('handler','default')
      ->setCardinality(1);

    $fields['stage'] = baseFieldDefinition::create('entity_reference')
      ->setLabel(t('Stage'))
      ->setSetting('target_type','stage')
      ->setSetting('handler','default')
      ->setCardinality(1);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
