<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Validated field entity.
 *
 * @see \Drupal\validated_fields\Entity\ValidatedField.
 */
class ValidatedFieldAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\ValidatedFieldInterface $entity */
    if($account->hasPermission('administer site configuration')){
      return AccessResult::allowed()->cachePerUser();
    }
    switch ($operation) {
      case 'view':
        if($account->id() == $entity->getAdminId()){
          return AccessResult::allowedIfHasPermission($account, 'administer validated field entities')->cachePerUser();
        }
        if($entity->getOwnerId() == $account->id()){
          return AccessResult::allowedIfHasPermission($account, 'view unpublished validated field entities')->cachePerUser();
        }
        if ($entity->isFinalized() &&in_array($account->id(),$entity->stage->entity->content_workflow->entity->getTalentIds())) {
          return AccessResult::allowedIfHasPermission($account, 'view published validated field entities')->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();
      case 'update':
      case 'delete':
        if($entity->isFinalized()){
          return AccessResult::neutral()->cachePerUser();
        }
        if($account->id() == $entity->getAdminId()){
          return AccessResult::allowedIfHasPermission($account, 'administer validated field entities')->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral()->cachePerUser();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer validated field entities');
  }


}
