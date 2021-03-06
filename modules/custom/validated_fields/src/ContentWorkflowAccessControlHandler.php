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

    if($account->hasPermission('administer site configuration')){
      return AccessResult::allowed()->cachePerUser();
    }
    if($account->id() == $entity->getOwnerId()){
      return AccessResult::allowed()->cachePerUser();
    }
    switch ($operation) {

      case 'view':

        if (in_array($account->id(), $entity->getTalentIds())) {
            return AccessResult::allowed()->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();

      case 'edit':
      case 'update':
      case 'delete':

        return AccessResult::neutral()->cachePerUser();
        
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral()->cachePerUser();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add content workflow entities');
  }


}
