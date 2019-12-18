<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Validated field entities.
 *
 * @ingroup validated_fields
 */
class ValidatedFieldListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Validated field ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\validated_fields\Entity\ValidatedField $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.validated_field.edit_form',
      ['validated_field' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
