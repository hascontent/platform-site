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
      return AccessResult::allowed();
    }

    if(!($account->id() == $entity->getAdminId()) && !in_array($account->id(),$entity->content_workflow->entity->getTalentIds())){
      return AccessResult::neutral();
    }
    switch ($operation) {

      case 'view':
        if(!$entity->isFinalized()){
          if($entity->getOwnerId() == $account->id() || $entity->getAdminId() == $account->id()){
            return AccessResult::allowed();
          }
        }
      case 'update':
      case 'delete':
        if($entity->isFinalized()){
          return AccessResult::neutral();
        }
        if(!isSet($entity->get('content_workflow')->target_id)){
          return AccessResult::allowedIfHasPermission($account, 'administer stage entities');
        }
        if($entity->getAdminId() == $account->id()){
          return AccessResult::allowedIfHasPermission($account, 'administer stage entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'administer stage entities');

    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer stage entities');
  }


}
