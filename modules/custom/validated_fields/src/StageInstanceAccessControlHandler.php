<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Stage Instance entity.
 *
 * @see \Drupal\validated_fields\Entity\StageInstance.
 */
class StageInstanceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\StageInstanceInterface $entity */
    if($account->id() === $entity->user_id->target_id){

      if($operation == 'view')
        return AccessResult::allowed()->cachePerUser();
    }


    if($account->id() == $entity->getAdminId()){
      switch ($operation) {

        case 'view':
  
          return AccessResult::allowed()->cachePerUser();
  
        case 'update':  
        case 'delete':
          if($entity->status->value < 1)
            return AccessResult::allowed()->cachePerUser();
      }
    }
   

    // Unknown operation, no opinion.
    return AccessResult::neutral()->cachePerUser();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::neutral();
  }


}
