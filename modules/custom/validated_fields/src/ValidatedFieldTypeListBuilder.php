<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Validated field type entities.
 *
 * @ingroup validated_fields
 */
class ValidatedFieldTypeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Validated field type ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\validated_fields\Entity\ValidatedFieldType $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.validated_field_type.edit_form',
      ['validated_field_type' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
