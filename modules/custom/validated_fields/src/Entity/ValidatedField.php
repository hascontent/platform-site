<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use mysql_xdevapi\Exception;

/**
 * Defines the Validated field entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "validated_field",
 *   label = @Translation("Validated field"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\ValidatedFieldListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\ValidatedFieldViewsData",
 *     "translation" = "Drupal\validated_fields\ValidatedFieldTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\ValidatedFieldForm",
 *       "add" = "Drupal\validated_fields\Form\ValidatedFieldForm",
 *       "edit" = "Drupal\validated_fields\Form\ValidatedFieldForm",
 *       "delete" = "Drupal\validated_fields\Form\ValidatedFieldDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\validated_fields\ValidatedFieldHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\ValidatedFieldAccessControlHandler",
 *   },
 *   base_table = "validated_field",
 *   data_table = "validated_field_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer validated field entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/validated-fields/validated_field/{validated_field}",
 *     "add-form" = "/validated-fields/validated_field/add",
 *     "edit-form" = "/validated-fields/validated_field/{validated_field}/edit",
 *     "delete-form" = "/validated-fields/validated_field/{validated_field}/delete",
 *     "collection" = "/validated-fields/validated_field",
 *   },
 *   field_ui_base_route = "validated_field.settings"
 * )
 */
class ValidatedField extends ContentEntityBase implements ValidatedFieldInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;
  /**
   * @var \Drupal\Core\Field\FieldItemListInterface
   */

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

  /**
   * Returns the field type of the referenced field store entity
   */
  public function getFieldType(){
    if(!isSet($this->storage->referencedEntities()[0])){
      throw new Exception("trying to reference field store entity that doesn't exist");
    }
    return $this->storage->referencedEntities()[0]->type->referencedEntities()[0]->id;
  }

  //TODO: test null values
  /**
   * Returns the value from the field store field
   * Note: because the field can be of any data type, it is important to know the field store type
   * use getFieldType to check
   */
  public function getFieldValue(){
    if(!isSet($this->storage->referencedEntities()[0])){
      throw new Exception("trying to reference field store entity that doesn't exist");
    }
    return $this->storage->referencedEntities()[0]->get("text")->value;
  }

  /**
   * Returns an associated array of validations in the form
   *  {
   *    "*validation_name*": {
   *        "*param1*": "value",
   *        "*param2*": "value"
   *     },
   *    "*validation_name*": {
   *       ...
   *     }
   *    ...
   *  }
   */
  public function getValidations(){
    return $this->validations->getValue()[0];
  }


  public function getStorageType(){
    return $this->field_type->referencedEntities()[0]->getStorageType();
  }
  /**
   * @param $name
   * @param $type
   * @return \Drupal\Core\Entity\EntityInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */

  public static function factory($name, $type){
    $vft = \Drupal::EntityTypeManager()->getStorage("validated_field_type")->loadByProperties(["name" => $type])[1];
    $entity = \Drupal::EntityTypeManager()->getStorage('validated_field')->create([
      "name" => $name,
      "validations" => $vft->validations->getValue()[0],
      "field_type" => $type->id->getValue()
    ]);
    $entity->save();
    return $entity;
  }


//  public function validateCollection(){
//
//  }
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Validated field entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
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
      ->setDescription(t('The name of the Validated field entity.'))
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

    $fields['status']->setDescription(t('A boolean indicating whether the Validated field is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    //my base fields
    $fields['validations'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Validations'))
      ->setDescription(t('Validations on the field'))
      ->setDisplayOptions('form' , [
        'label' => 'Validations',
        'weight' => -1,
        'type' => 'map_assoc_widget'
      ]);
    $fields['permission_level'] = BaseFieldDefinition::create('integer')
        ->setLabel(t("Permission Level"))
        ->setDescription(t("Sets amount of permissions owner has"))
        ->addPropertyConstraints('value',['Range'=> ['min' => 0, 'max' => 3]])
        ->setDefaultValue(0)
        ->setDisplayOptions('form', [
          'type' => 'number'
        ]);
    $fields['storage'] = BaseFieldDefinition::create("entity_reference")
      ->setLabel(t('Storage'))
      ->setDescription(t('The Field Store Object holding data'))
      ->setSetting('target_type','field_store')
      ->setSetting('handler','default')
//      ->setDisplayOptions('form', array(
//        'type'     => 'entity_reference_autocomplete',
//        'weight'   => 5,
//        'settings' => array(
//          'match_operator'    => 'CONTAINS',
//          'size'              => '60',
//          'autocomplete_type' => 'tags',
//          'placeholder'       => '',
//        ),
//      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['comments'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Comments'))
      ->setDescription(t('comments on field'))
      ->setDisplayOptions('form' , [
        'label' => 'Validations',
        'weight' => -1,
        'type' => 'map_assoc_widget'
      ]);

    $fields['field_type'] = BaseFieldDefinition::create("entity_reference")
      ->setLabel(t('Type'))
      ->setDescription(t('Validated Field Type entity associated with this validated field'))
      ->setSetting('target_type','validated_field_type')
      ->setSetting('handler','default')
      ->setDisplayOptions('form', array(
        'type'     => 'entity_reference_autocomplete',
        'weight'   => 5,
        'settings' => array(
          'match_operator'    => 'CONTAINS',
          'size'              => '60',
          'autocomplete_type' => 'tags',
          'placeholder'       => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);
    return $fields;
  }

}
