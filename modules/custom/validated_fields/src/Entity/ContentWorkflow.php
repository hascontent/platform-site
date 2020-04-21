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
 * Defines the Content Workflow entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "content_workflow",
 *   label = @Translation("Content Workflow"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\ContentWorkflowListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\ContentWorkflowViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\ContentWorkflowForm",
 *       "add" = "Drupal\validated_fields\Form\ContentWorkflowForm",
 *       "edit" = "Drupal\validated_fields\Form\ContentWorkflowForm",
 *       "delete" = "Drupal\validated_fields\Form\ContentWorkflowDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\validated_fields\ContentWorkflowHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\ContentWorkflowAccessControlHandler",
 *   },
 *   base_table = "content_workflow",
 *   translatable = FALSE,
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/validated-fields/contentworkflow/content_workflow/{content_workflow}",
 *     "add-form" = "/vf/cw/add",
 *     "edit-form" = "/vf/cw/{content_workflow}/edit",
 *     "delete-form" = "/vf/cw/{content_workflow}/delete",
 *     "collection" = "/vf/cw/list",
 *   },
 *   field_ui_base_route = "content_workflow.settings"
 * )
 */
class ContentWorkflow extends ContentEntityBase implements ContentWorkflowInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

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
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

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

  public function getTalentIds(){
    $ret = [];
    foreach($this->get("talent")->getValue() as $val){
      array_push($ret, $val["target_id"]);
    }
    return $ret;
  }
  // Insert stage entity into middle of stage list
  public function insertStage($stage, $offset){
    $stages = $this->stages;
    if(!$stages->offsetExists($offset)){
      $stages->appendItem($stage);
      return $this;
    }
    while($offset < $this->stages->count()){
      $tmp = $stages->offsetGet($offset)->target_id;
      $stages->offsetSet($offset, $stage);
      $stage = $tmp;
      $offset++;
    }
    $stages->appendItem($stage);
    return $this;
  }

  //Delete All Stage Instances
  protected function deleteStageInstances(){
    $curr = $this->stages->offsetGet(0)->entity->stage_instances->offsetGet(0)->entity;
    while($curr){
      $next = $curr->next_stage->entity;
      $curr->delete();
      $curr = $next;
    }
  }

  // Rebuild all stage instances
  public function rebuildStageInstances($start_date = null){
    if(isSet($this->stages->entity->stage_instances->entity)){
      $this->deleteStageInstances();
    }
    $itr = $this->stages->getIterator();
    $prev = null;
    while($itr->valid()){
      $curr = $itr->current()->entity->createInstance(null, $prev, $start_date );
      if(!$curr->save()){
        throw \Exception("could not save stage instance in rebuildStageInstances()");
      }
      $prev = $curr;
      $start_date = $curr->estimated_due_date->value;
      $itr->next();
    }
  }
  // Move Stage from one index to another
  public function moveStage($old_offset, $new_offset){
    $stage_id = $this->stages->offsetGet($old_offset)->target_id;
    $this->stages->removeItem($old_offset);
    return $this->insertStage($stage_id,$new_offset);
  }
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Content Workflow entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Content Workflow entity.'))
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

    $fields['status']->setDescription(t('A boolean indicating whether the Content Workflow is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ])
      ->setDefaultValue(FALSE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['stages'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Stages'))
      ->setDescription(t('Stages of the Workflow'))
      ->setSetting('target_type','stage')
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
      ->setCardinality(-1); //infinite cardinality
    
    $fields['talent'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Team members'))
      ->setDescription(t('The team members working on this piece of content'))
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

    $fields['current_stage'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type','stage')
      ->setSetting('handler','default')
      ->setCardinality(1);

    $fields['workflow_template'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type','workflow_template')
      ->setSetting('handler','default')
      ->setCardinality(1)
      // ->setRequired(TRUE)
      ;

    return $fields;
  }

}
