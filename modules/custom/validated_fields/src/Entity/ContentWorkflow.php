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
  protected function rebuildStageInstances($start_date = null){
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

  // activate workflow
  public function activateWorkflow($start_date = null){
    //if there are no stages in the workflow error
    if($this->stages->count() < 1){
      $name = $this->name->value;
      throw new \Exception("No stages in workflow: $name");
    }
    $this->rebuildStageInstances($start_date);
    $this->current_stage = 0;
    $this->save();
    $stage_id = $this->stages->target_id;
    $inst = \Drupal::EntityTypeManager()->getStorage("stage")->loadUnchanged($stage_id)->stage_instances->entity;
    $inst->status = 1;
    $inst->save();
  }
  // Move Stage from one index to another
  public function moveStage($old_offset, $new_offset){
    $stage_id = $this->stages->offsetGet($old_offset)->target_id;
    $this->stages->removeItem($old_offset);
    return $this->insertStage($stage_id,$new_offset);
  }  

  // Use stage transition
  //TODO: add machine user to represent automatic stage transitions
  public function transitionStage($stage_action_ind, $user_id = null){
    $current_stage_index = $this->current_stage->value;
    if($current_stage_index == null){
        return "Workflow has not been activated yet";
    }
    $stage = $this->stages->offsetGet($current_stage_index)->entity;
    $current_stage_instance = $stage->stage_instances->offsetGet($stage->stage_instances->count()-1)->entity;

    // check if user is owner of stage
    if($user_id !== null && $current_stage_instance->getOwnerId() != $user_id){
        throw new AccessDeniedHttpException();
    }
    if($user_id == null){
      $user_id = 0;
    }
    try{
        $action = $stage->actions->offsetGet($stage_action_ind)->entity;

        // check if action can be used
        if($action->uses->value <= $action->records->count()){
            return "Action has reached number of uses";
        }
        $action->uses->value = $action->uses->value - 1;
        $action->save();
        $target_stage_index = null;
        // trigger events
        $action->triggerEvents();

        $target_stage = $action->target_stage->entity;

        //in case target_stage is completion
        if($target_stage !== null && $target_stage->id() == $this->final_stage->target_id){
            $current_stage_instance->status = 2;
            $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
            $current_stage_instance->save();
            $this->current_stage = -2;
            $this->save();
            return;
        }
        // figure out where the target stage appears in relation to the current stage in the stage order and modify linked list
        if($target_stage !== null){
            for($i = 0; $i < $this->stages->count(); $i++){
                if($this->stages->offsetGet($i)->target_id == $target_stage->id()){
                    $target_stage_index = $i;
                break;
                }
            }
        } else {
            // in the event that no target stage is given, assume it is the next stage
            $target_stage_index = $current_stage_index + 1;
        }
        //if the target stage is the same as the current stage, return without doing any stage transitions
        if($target_stage_index == $current_stage_index){
            return "No stage transition performed";
        }
        //if the target stage appears before the current stage create copies of the stages between to lead back to the current stage
        if($target_stage_index < $current_stage_index){
            $old_next_stage = $current_stage_instance->next_stage->entity;
            $target_stage_instance = $target_stage->createInstance(null, $current_stage_instance);
            $target_stage_instance->save();
            $current_stage_instance->next_stage = $target_stage_instance;
            $current_stage_instance->save();
            $prev_stage_instance = $target_stage_instance;
            $stage_instance = null;
            for($i = $target_stage_index + 1; $i <= $current_stage_index; $i++){
                $stage_instance = $this->stages[$i]->entity->createInstance(null, $prev_stage_instance);
                $stage_instance->save();
                $prev_stage_instance = $stage_instance;
            }
            $stage_instance->next_stage = $old_next_stage;
            $stage_instance->save();
            $old_next_stage->prev_stage = $stage_instance;
            $old_next_stage->save();
        }
        // stage transition for pushing passed the next stage to an upcoming stage
        elseif($target_stage_index > $current_stage_index + 1){
            $stage_instance = $current_stage_instance->next_stage->entity;
            while($stage_instance->stage_template->target_id !== $target_stage->id()){
                $prev_stage_instance = $stage_instance;
                $stage_instance = $stage_instance->next_stage->entity;
                $prev_stage_instance->delete();
            }
            $stage_instance->prev_stage = $current_stage_instance;
            $stage_instance->save();
            $current_stage_instance->next_stage = $stage_instance;
            $current_stage_instance->save();
        }

        // if the target stage index appears right after the current stage index do a basic stage transition
        if($target_stage_index == ($current_stage_index + 1)){
            //if current stage is last stage go to completion stage
            if($current_stage_index == $this->stages->count() - 1){
                $current_stage_instance->status = 2;
                $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
                $current_stage_instance->save();
                $this->current_stage = -2;
                $this->save();

            }
        }

    } catch(\Exception $e) {
        return ["Error" => $e->getMessage()];
    }

    // update the status and completion date of the current and next stage
    $record = $action->createRecord($user_id);
    $current_stage_instance->action_record = $record;
    $current_stage_instance->status->value = 2;
    $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
    $current_stage_instance->save();

    $next_stage_instance = $current_stage_instance->next_stage->entity;
    if($next_stage_instance !== null){
      $next_stage_instance->status->value = 1;
      $next_stage_instance->save();
    }
    $this->set("current_stage", $target_stage_index);
    $this->save();
    //update due dates
    $current_stage_instance->cascadeDueDates(true);

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

    // special values: -1 = not activated
    //  -2 = completed
    $fields['current_stage'] = BaseFieldDefinition::create('integer')
      ->setSetting('handler','default')
      ->setCardinality(1)
      ->setDefaultValue(-1);

    $fields['is_template'] = BaseFieldDefinition::create("boolean")
      ->setLabel('Template Field')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ])
      ->setDefaultValue(FALSE);

    $fields['stage_fields'] = BasefieldDefinition::create('entity_reference')
      ->setLabel(t('Validated Fields'))
      ->setDescription(t('The Validated Field Entities'))
      ->setSetting('target_type','validated_field_type')
      ->setSetting('handler','default')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setCardinality(-1);

    $fields["final_stage"] = BaseFieldDefinition::create('entity_reference')
        ->setSetting('target_type', 'stage')
        ->setSetting('handler','default')
        ->setCardinality(1);

    return $fields;
  }

}
