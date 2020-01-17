<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Content Workflow entities.
 *
 * @ingroup validated_fields
 */
class ContentWorkflowListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Content Workflow ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\validated_fields\Entity\ContentWorkflow $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.content_workflow.edit_form',
      ['content_workflow' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
