<?php
  namespace Drupal\validated_fields\Entity;

  use Drupal\validated_fields\FieldStoreInterface;
  use Drupal\Core\Entity\EntityStorageInterface;
  use Drupal\Core\Field\BaseFieldDefinition;
  use Drupal\Core\Entity\ContentEntityBase;
  use Drupal\Core\Entity\EntityTypeInterface;
  use Drupal\user\UserInterface;

  /**
 * Defines the ContentEntityExample entity.
 *
 * @ingroup content_entity_example
 *
 * [...]
 *
 *  The following annotation is the actual definition of the entity type which
 *  is read and cached. Don't forget to clear cache after changes.
 *
 *  *     "form" = {
 *       "add" = "Drupal\validated_fields\Form\FieldStoreForm",
 *       "edit" = "Drupal\validated_fields\Form\FieldStoreForm",
 *       "delete" = "Drupal\validated_fields\Form\FieldStoreDeleteForm",
 *     },
 * @ContentEntityType(
 *   id = "field_store",
 *   label = "Field Store",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\validated_fields\Entity\Controller\FieldStoreListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\validated_fields\FieldStoreAccessControlHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "field_store",
 *   admin_permission = "administer validated field entity types",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *   },
 *   links = {
 *     "canonical" = "/validated-fields/sfe/{field_store}",
 *     "edit-form" = "/validated-fields/sfe/{field_store}/edit",
 *     "delete-form" = "/validated-fields/sfe/{field_store}/delete",
 *     "collection" = "/validated-fields/sfe/list"
 *   },
 *
 *   field_ui_base_route = "entity.field_store_type.edit_form",
 *   bundle_entity_type = "field_store_type",
 *   bundle_label = @Translation("Field Store Type"),
 *   translatable = FALSE,
 * )
 *
 *
 */

class FieldStore extends ContentEntityBase implements FieldStoreInterface{
  public function getTypeId(){
    return $this->type->target_id;
  }

  public function getValue(){
    return $this->get($this->getTypeId())->value;
  }

  public function setValue($value){
    $this->set($this->getTypeId(),$value);
    return $this;
  }

  public function getParentId(){
    return $this->validated_field->target_id;
  }
  public function getParent(){
    $this->get('validated_field')->entity;
  }
/**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the field store entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel('UUID')
      ->setDescription(t('The UUID of the field store entity.'))
      ->setReadOnly(TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel('Type')
      ->setDescription(t('The field store type.'))
      ->setSetting('target_type', 'field_store_type')
      ->setReadOnly(TRUE);

      //if recommenting add label back to entity keys annotation: "label" = "label",
    // Name field for the field store.
    // We set display options for the view as well as the form.
    // Users with correct privileges can change the view and edit configuration.
    // $fields['label'] = BaseFieldDefinition::create('string')
    //   ->setLabel('Label')
    //   ->setDescription(t('The name of the store entity.'))
    //   ->setSettings(array(
    //     'default_value' => '',
    //     'max_length' => 255,
    //     'text_processing' => 0,
    //   ))
    //   ->setDisplayOptions('view', array(
    //     'label' => 'above',
    //     'type' => 'string',
    //     'weight' => -6,
    //   ))
    //   ->setDisplayOptions('form', array(
    //     'type' => 'string_textfield',
    //     'weight' => -6,
    //   ))
    //   ->setDisplayConfigurable('form', TRUE)
    //   ->setDisplayConfigurable('view', TRUE);

    $fields['validated_field'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel('Validated Field')
      ->setDescription(t('The Validated Field this field store is associated with'))
      ->setSetting('target_type', 'validated_field')
      ->setReadOnly(TRUE);


    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel('Language code')
      ->setDescription(t('The field store entity language code.'))
      ->setRevisionable(TRUE);
    return $fields;
    }
}
?>
