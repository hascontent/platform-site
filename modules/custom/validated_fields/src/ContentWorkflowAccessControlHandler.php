<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Content Workflow entity.
 *
 * @see \Drupal\validated_fields\Entity\ContentWorkflow.
 */
class ContentWorkflowAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\ContentWorkflowInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished() && in_array($account->id(), $entity->getTalentIds())) {
            return AccessResult::allowedIfHasPermission($account, 'view unpublished content workflow entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published content workflow entities');
      case 'edit':
      case 'update':
      case 'delete':
        if($account->id() == $entity->getOwnerId()){
          return AccessResult::allowedIfHasPermission($account, 'administer content workflow entities');
        }
        return AccessResult::neutral();
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add content workflow entities');
  }


}
