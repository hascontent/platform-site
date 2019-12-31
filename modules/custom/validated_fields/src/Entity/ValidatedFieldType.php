<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Validated field type entity.
 *
 * @ingroup validated_fields
 *
 * @ContentEntityType(
 *   id = "validated_field_type",
 *   label = @Translation("Validated field type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\ValidatedFieldTypeListBuilder",
 *     "views_data" = "Drupal\validated_fields\Entity\ValidatedFieldTypeViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\ValidatedFieldTypeForm",
 *       "add" = "Drupal\validated_fields\Form\ValidatedFieldTypeForm",
 *       "edit" = "Drupal\validated_fields\Form\ValidatedFieldTypeForm",
 *       "delete" = "Drupal\validated_fields\Form\ValidatedFieldTypeDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\validated_fields\ValidatedFieldTypeHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\ValidatedFieldTypeAccessControlHandler",
 *   },
 *   base_table = "validated_field_type",
 *   translatable = FALSE,
 *   admin_permission = "administer validated field type entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/validated-fields/validated_field_type/{validated_field_type}",
 *     "add-form" = "/validated-fields/validated_field_type/add",
 *     "edit-form" = "/validated-fields/validated_field_type/{validated_field_type}/edit",
 *     "delete-form" = "/validated-fields/validated_field_type/{validated_field_type}/delete",
 *     "collection" = "/validated-fields/validated_field_type",
 *   },
 *   field_ui_base_route = "validated_field_type.settings"
 * )
 */
class ValidatedFieldType extends ContentEntityBase implements ValidatedFieldTypeInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   *
   * factory method for testing purposes
   * @param $name
   * @param $validations
   * @return \Drupal\Core\Entity\EntityInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function factory($name, $validations){
    $entity = \Drupal::EntityTypeManager()->getStorage('validated_field_type')->create([
      "name" => $name,
      "validations" => $validations,
      "field_type" => "text"
    ]);
    $entity->save();
    return $entity;
  }

  //////////////////////////////////////////////////
  /*
   * accessors
   */

   /*
    * returns the referenced field store id
    */
  public function getStorageTypeId(){
    if(!isSet($this->field_type->target_id)){
      return null;
    }
    return $this->field_type->target_id;
  }

  public function getValidations(){
    return $this->validations->getValue()[0];
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Validated field type entity.'))
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

    $fields['status']->setDescription(t('A boolean indicating whether the Validated field type is published.'))
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

    $fields['field_type'] = BaseFieldDefinition::create("entity_reference")
      ->setLabel(t('Type'))
      ->setDescription(t('The Type of Field this entity stores'))
      ->setSetting('target_type','field_store_type')
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
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    $fields['validations'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Default Validations'))
      ->setDescription(t('Default Validations on the field'))
      ->setDisplayOptions('form' , [
        'label' => 'Validations',
        'weight' => -1,
        'type' => 'map_assoc_widget'
      ]);

    return $fields;
  }

}
