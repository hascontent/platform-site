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

    switch ($operation) {
      
      case 'view':

        return AccessResult::allowedIfHasPermission($account, 'preview stage entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'administer stage entities');

      case 'delete':

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
