<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\validated_fields\Entity\ValidatedFieldInterface;
/**
 * Defines the Stage entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "stage",
 *   label = @Translation("Stage"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\StageListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\StageViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\StageForm",
 *       "add" = "Drupal\validated_fields\Form\StageForm",
 *       "edit" = "Drupal\validated_fields\Form\StageForm",
 *       "delete" = "Drupal\validated_fields\Form\StageDeleteForm",
 *     },
 *     "access" = "Drupal\validated_fields\StageAccessControlHandler",
 *   },
 *   base_table = "stage",
 *   translatable = FALSE,
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/vf/s/{stage}",
 *     "add-form" = "/vf/s/add",
 *     "edit-form" = "/vf/s/{stage}/edit",
 *     "delete-form" = "/vf/s/{stage}/delete",
 *     "collection" = "/vf/s/list",
 *   },
 * )
 */
class Stage extends ContentEntityBase implements StageInterface {

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



  public function getAdminId(){
    return $this->get('content_workflow')->entity->getOwnerId();
  }

  public function getAdmin(){
    return $this->get('content_workflow')->entity->getOwner();
  }



  public function getTalentIds(){
    return $this->get('content_workflow')->entity->getTalentIds();
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);


    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Stage entity.'))
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

    $fields['content_workflow'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Workflow'))
      ->setDescription(t('The workflow this stage is a part of'))
      ->setSetting('target_type','content_workflow')
      ->setSetting('handler','default')
      ->setReadOnly(TRUE)
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
      ->setRequired(TRUE)
      ->setCardinality(1);

    $fields['actions'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Actions'))
      ->setDescription(t('Actions allow users to perform certain actions from there stage'))
      ->setSetting('target_type','stage_action')
      ->setSetting('handler','default')
      ->setCardinality(-1);
  
    $fields['stage_instances'] = baseFieldDefinition::create('entity_reference')
      ->setLabel(t('Stage Instances'))
      ->setSetting('target_type','stage_instance')
      ->setSetting('handler','default')
      ->setCardinality(-1);
    
    $fields['time_interval'] = baseFieldDefinition::create('interval')
      ->setLabel(t("Default Time To Complete Stage"))
      ->setSetting('hanndler','default')
      ->setDefaultValue(["interval" => 2, "period" => "day"]);


    return $fields;
  }

}
