<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Action Record entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "action_record",
 *   label = @Translation("Action Record"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\ActionRecordListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\ActionRecordViewsData",
 *
 *     "access" = "Drupal\validated_fields\ActionRecordAccessControlHandler",
 *   },
 *   base_table = "action_record",
 *   translatable = FALSE,
 *   admin_permission = "administer action record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   },
 * )
 */
class ActionRecord extends ContentEntityBase implements ActionRecordInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  // /**
  //  * {@inheritdoc}
  //  */
  // public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
  //   parent::preCreate($storage_controller, $values);
  //   $values += [
  //     'user_id' => \Drupal::currentUser()->id(),
  //   ];
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

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getOwner() {
  //   return $this->get('user_id')->entity;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getOwnerId() {
  //   return $this->get('user_id')->target_id;
  // }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Action Record entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
