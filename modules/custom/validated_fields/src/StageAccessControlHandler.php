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

    if(!($account->id() == $entity->getAdminId()) && !in_array($account->id(),$entity->content_workflow->entity->getTalentIds())){
      return AccessResult::neutral()->cachePerUser();
    }
    switch ($operation) {

      case 'view':
        if(!$entity->isFinalized()){
          if($entity->getOwnerId() == $account->id() || $entity->getAdminId() == $account->id()){
            return AccessResult::allowed()->cachePerUser();
          }
        }
        if(in_array($account->id(),$entity->getTalentIds()) || $account->id() == $entity->getAdminId()){
          return AccessResult::allowed()->cachePerUser();
        }
        return AccessResult::neutral()->cachePerUser();

      case 'update':
      case 'delete':
        if($entity->isFinalized()){
          return AccessResult::neutral()->cachePerUser();
        }
        if(!isSet($entity->get('content_workflow')->target_id)){
          return AccessResult::allowedIfHasPermission($account, 'administer stage entities')->cachePerUser();
        }
        if($entity->getAdminId() == $account->id()){
          return AccessResult::allowedIfHasPermission($account, 'administer stage entities')->cachePerUser();
        }
        return AccessResult::allowedIfHasPermission($account, 'administer stage entities')->cachePerUser();

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


}
