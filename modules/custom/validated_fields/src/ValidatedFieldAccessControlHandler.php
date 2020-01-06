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

    switch ($operation) {

      case 'view':
        if(AccessResult::allowedIfHasPermisssion($account, 'administer validated field entities')->isAllowed()){
          return AccessResult::allowed();
        }
        if (!$entity->isPublished()) {
          if($entity->getOwnerId() == $account->id){
          return AccessResult::allowedIfHasPermission($account, 'view unpublished validated field entities');
          } else {
            return AccessResult::neutral();
          }
        }


        return AccessResult::allowedIfHasPermission($account, 'view published validated field entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'administer validated field entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'administer validated field entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer validated field entities');
  }


}
