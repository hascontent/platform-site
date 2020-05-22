<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageInterface;
/**
 * Defines the Stage Instance entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "stage_instance",
 *   label = @Translation("Stage Instance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\StageInstanceListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\StageInstanceViewsData",
 *
 *     "access" = "Drupal\validated_fields\StageInstanceAccessControlHandler",
 *   },
 *   base_table = "stage_instance",
 *   translatable = FALSE,
 *   admin_permission = "administer stage instance entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   },
 * )
 */
class StageInstance extends ContentEntityBase implements StageInstanceInterface {

  use EntityChangedTrait;


  /**
   * {@inheritdoc}
   */
  // public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
  //   parent::preCreate($storage_controller, $values);
  //   $values += [
  //     'user_id' => \Drupal::currentUser()->id(),
  //   ];
  // }

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

  public function isFinalized(){
    return $this->isPublished();
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

  //Date Accessors
  public function getTimeInterval(){
    return $this->stage_template->entity->time_interval->offsetGet(0);
  }

  // set start and due dates of succeeding stage instances after this stage
  public function cascadeDueDates($first = true){
    $next_start_date = $first ? $this->complete_date->value : $this->due_date->value;
    $next_stage = $this->next_stage->entity;
    if($next_stage == null){
      return;
    } else {
      $next_stage->start_date->value = $next_start_date;
      $next_stage->setDueFromStart();
      $next_stage->save();
      $next_stage->cascadeDueDates(false); 
    }
  }
  // Set End From Start sets the end date based on the start date and the time interval given to complete the task
  public function setEstimatedDueFromStart(){
    $holidays = []; // replaced by actual holidays array
    $estimated_start_date = clone $this->estimated_start_date->date;
    $estimated_due_date = StageInstance::addInterval($estimated_start_date, $this->getTimeInterval()->getInterval());
    $this->set("estimated_due_date",StageInstance::DDTtoDTI($estimated_due_date));
  }
  public function setDueFromStart(){
    $start_date = clone $this->start_date->date;
    $due_date = StageInstance::addInterval($start_date, $this->getTimeInterval()->getInterval());
    $this->set("due_date",StageInstance::DDTtoDTI($due_date));
  }

  // var: DrupalDateTime
  public static function addInterval($start, $interval){
    $holidays = []; //array of holidays
    $skip_weekends = \Drupal::config('validated_fields.settings')->get('interval_skip_weekends');
    if($skip_weekends){
      $old_interval = $interval;
      for($int = 1; $int <= $interval; $int++){
        $date = clone $start;
        $date->add(new \DateInterval("P" . $int . "D"));
        if($date->format('w') == 0 || $date->format('w') == 6){
          $interval++;
          continue;
        }
        for($i = 0; $i < sizeof($holidays); $i++){
          if($holidays[$i] == $date->format('w')){
            $interval++;
            continue;
          }
        }
      }
    }
    $date = clone $start;
    return $date->add(\DateInterval::createFromDateString($interval . " days"));
  }

  public function setCompleted(){
    $this->complete_date = new DateTime();
  }
  // convert DrupalDateTime object to DateTimeItem readable input
  public static function DDTtoDTI($ddt){
    $str = $ddt->render();
    $arr = explode(" ", $str);
    return $arr[0] . "T" . $arr[1];
  }

  public function setFields(){
    $template_fields = $this->stage_template->entity->content_workflow->entity->workflow_template->entity->stage_fields;
    $vf_manager = \Drupal::EntityTypeManager()->getStorage('validated_field');
    for($i = 0; $i < $template_fields->count();$i++){
      $field = $vf_manager->create(["field_type" => $template_fields[$i]->entity, "stage" => $this]);
      $field->save();
      $this->validated_fields->appendItem($field);
    }
  }
  public function getAdminId(){
    return $this->get('stage_template')->entity->getAdminId();
  }

  // TODO: create action to transition to next stage
  public function createAction($params){
    
  }
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Stage Instance entity.'))
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

    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t("Status"))
      ->setDescription(t('A integer indicating the state of the stage instance.'))
      ->addPropertyConstraints('value',['Range'=> ['min' => 0, 'max' => 2]])
      ->setDefaultValue(0)
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

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

    $fields['validated_fields'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Validated Fields'))
      ->setDescription(t('The Validated Field Entities'))
      ->setSetting('target_type','validated_field')
      ->setSetting('handler','default')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setCardinality(-1); //infinite cardinality

    $fields["stage_template"] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Stage Template"))
      ->setDescription(t("The settings and name of the stage"))
      ->setSetting('target_type','stage')
      ->setRequired(TRUE)
      ->setSetting('handler','default');


    $fields["estimated_start_date"] = BaseFieldDefinition::create('datetime')
      ->setLabel(t("Original Start Date"))
      ->setDescription(t("The Original Start Date"))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);

    $fields["estimated_due_date"] = BaseFieldDefinition::create('datetime')
      ->setLabel(t("Original Due Date"))
      ->setDescription(t("The Original Due Date"))
      ->setReadOnly(TRUE);

    $fields["start_date"] = BaseFieldDefinition::create('datetime')
      ->setLabel(t("Estimated/Actual Start Date"))
      ->setDescription(t("The Estimated/Actual Start Date"))
      ->setReadOnly(TRUE);

    $fields["due_date"] = BaseFieldDefinition::create('datetime')
    ->setLabel(t("Estimated/Actual Due Date"))
    ->setDescription(t("The Estimated/Actual Due Date"))
    ->setReadOnly(TRUE);

    $fields["complete_date"] = BaseFieldDefinition::create('datetime')
    ->setLabel(t("Estimated/Actual Start Date"))
    ->setDescription(t("The Estimated/Actual Start Date"))
    ->setReadOnly(TRUE);

    $fields["next_stage"] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Next Stage"))
      ->setDescription(t("The Next Stage"))
      ->setSetting('target_type','stage_instance');

    $fields["prev_stage"] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Previous Stage"))
      ->setDescription(t("The Previous Stage"))
      ->setSetting('target_type','stage_instance');

    $fields["action_record"] = baseFieldDefinition::create('entity_reference')
      ->setLabel(t("Action Record"))
      ->setDescription(t("Record of the action taken at the end of this stage"))
      ->setSetting('target_type','action_record');
    return $fields;
  }

}
