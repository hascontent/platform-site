<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
/**
 * Field Store Type
 * allows fields to be attached to field stores
 *
 * @ConfigEntityType(
 *   id = "field_store_type",
 *   label = @Translation("Field Store Type"),
 *   bundle_of = "field_store",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "field_store_type",
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\validated_fields\Form\FieldStoreTypeForm",
 *       "add" = "Drupal\validated_fields\Form\FieldStoreTypeForm",
 *       "edit" = "Drupal\validated_fields\Form\FieldStoreTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\validated_fields\FieldStoreTypeAccessControlHandler",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/field_store_type/{field_store_type}",
 *     "add-form" = "/admin/structure/field_store_type/add",
 *     "edit-form" = "/admin/structure/field_store_type/{field_store_type}/edit",
 *     "delete-form" = "/admin/structure/field_store_type/{field_store_type}/delete",
 *     "collection" = "/admin/structure/field_store_type",
 *   }
 * )
 *
 */

 class FieldStoreType extends ConfigEntityBundleBase {}
?>
