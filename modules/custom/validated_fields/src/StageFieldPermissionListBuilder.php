<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Stage field permission entities.
 *
 * @ingroup validated_fields
 */
class StageFieldPermissionListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Stage field permission ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\validated_fields\Entity\StageFieldPermission $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.stage_field_permission.edit_form',
      ['stage_field_permission' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
