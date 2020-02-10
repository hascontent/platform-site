<?php

namespace Drupal\validated_fields;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the contact entity.
 */
class FieldStoreAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   *
   * Link the activities to the permissions. checkAccess() is called with the
   * $operation as defined in the routing.yml file.
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Check the admin_permission as defined in your @ContentEntityType
    // annotation.
    $admin_permission = $this->entityType->getAdminPermission();
    if ($account->hasPermission($admin_permission)) {
      return AccessResult::allowed()->cachePerUser();
    }
    switch ($operation) {
      case 'view':
        if($account->id() == $entity->getOwnerId() && $entity->getParent()->getPermissionLevel() > 0){
          return AccessResult::allowedIfHasPermission($account, 'view unpublished validated field entities')->cachePerUser();
        }
        if($account->id() == $entity->getAdminId()){
          return AccessResult::allowedIfHasPermission($account, "administer validated field entities")->cachePerUser();
        }
        if ($entity->isFinalized() &&in_array($account->id(),$entity->validated_field->entity->stage->entity->content_workflow->entity->getTalentIds())) {
          return AccessResult::allowedIfHasPermission($account, 'view published validated field entities')->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();
      case 'update':
        if($entity->isFinalized()){
          return AccessResult::neutral()->cachePerUser();
        }
        if($account->id() == $entity->getOwnerId() && $entity->getParent()->getPermissionLevel() > 1){
          return AccessResult::allowedIfHasPermission($account, 'view unpublished validated field entities')->cachePerUser();
        }
        if($account->id() == $entity->getAdminId()){
          return AccessResult::allowedIfHasPermission($account, 'administer validated field entities')->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, $admin_permission)->cachePerUser();
    }
    return AccessResult::neutral()->cachePerUser();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist. It
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // Check the admin_permission as defined in your @ContentEntityType
    // annotation.
    $admin_permission = $this->entityType->getAdminPermission();
    if ($account->hasPermission($admin_permission)) {
      return AccessResult::allowed()->cachePerUser();
    }
    return AccessResult::allowedIfHasPermission($account, 'administer validated field entities');
  }

}
