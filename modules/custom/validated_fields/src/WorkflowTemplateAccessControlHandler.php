<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Workflow template entity.
 *
 * @see \Drupal\validated_fields\Entity\WorkflowTemplate.
 */
class WorkflowTemplateAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\WorkflowTemplateInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished workflow template entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published workflow template entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit workflow template entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete workflow template entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add workflow template entities');
  }


}
