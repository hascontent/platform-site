<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Stage entity.
 *
 * @see \Drupal\validated_fields\Entity\Stage.
 */
class StageAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\StageInterface $entity */
    if($account->hasPermission('administer site configuration')){
      return AccessResult::allowed()->cachePerUser();
    }

    switch ($operation) {

      case 'view':
        $user_in_uids = false;
        for($i = 0; $i < $entity->user_id->count(); $i++){
          if($account->id() == $entity->user_id[$i]->target_id){
            $user_in_uids = true;
          }
        }
        if($user_in_uids)
          return AccessResult::allowed()->cachePerUser();
      case 'update':
        return StageAccessControlHandler::checkAdminPermission($entity, $account);
      case 'delete':
        if($entity->stage_instances->count() < 1){
          return StageAccessControlHandler::checkAdminPermission($entity, $account);
        }
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral()->cachePerUser();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer stage entities');
  }

  // checks if user has admin permission and returns access result
  private static function checkAdminPermission($entity, $account){
    if(!isSet($entity->get('content_workflow')->target_id)){
      return AccessResult::allowedIfHasPermission($account, 'administer stage entities')->cachePerUser();
    }
    if($entity->getAdminId() == $account->id()){
      return AccessResult::allowedIfHasPermission($account, 'administer stage entities')->cachePerUser();
    }
    return AccessResult::neutral()->cachePerUser();
  }
}