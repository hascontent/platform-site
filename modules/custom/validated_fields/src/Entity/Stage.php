<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\validated_fields\Entity\ValidatedFieldInterface;
use Drupal\Core\Datetime\DrupalDateTime;

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

  const __COMPLETE__ = "__COMPLETE__";
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

  public function getOwnerId(){
    return $this->get('user_id')->target_id;
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

  public function createInstance($next = null, $previous = null, $date = null, array $overrides = []){
    $options['next_stage'] = $next;
    $options['prev_stage'] = $previous;
    $options['estimated_start_date'] = $date;
    if($date == null){
      $options['estimated_start_date'] = StageInstance::DDTtoDTI(new DrupalDateTime());
    }
    $options['stage_template'] = $this->id();
    return \Drupal::EntityTypeManager()->getStorage('stage_instance')->create($options);

  }

  public function createAction($target_stage_index = null, $name = "next", $triggered_events = [], $options = []){

    $options["target_stage"] = $this->content_workflow->entity->stages[$target_stage_index]->target_id;
    $options["name"] = $name;
    $options["triggered_events"] = $triggered_events;
    $action = \Drupal::EntityTypeManager()->getStorage("stage_action")->create($options);
    $action->save();
    $this->actions->appendItem($action);
    return $action;
  }
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Stage entity.'))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setCardinality(-1);
      
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

    $fields['auto_advance'] = baseFieldDefinition::create('boolean')
      ->setLabel("Auto Advance")
      ->setDescription("Decide whether to auto advance the stage once it hits its due date")
      ->setDefaultValue(FALSE);

    return $fields;
  }

}
