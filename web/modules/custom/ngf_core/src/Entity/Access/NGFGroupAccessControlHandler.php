<?php

namespace Drupal\ngf_core\Entity\Access;

use Drupal\group\Entity\Access\GroupAccessControlHandler;
use Drupal\group\Access\GroupAccessResult;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the custom access control handler for the user entity type.
 */
class NGFGroupAccessControlHandler extends GroupAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($entity->hasField('field_ngf_group_visibility')) {
      $groupVisibility = $entity->get('field_ngf_group_visibility')->getString();
    }
    else {
      $groupVisibility = NGF_GROUP_PUBLIC;
    }

    switch ($operation) {
      case 'view':
        // Block access if group is private and user is not a member.
        if ($groupVisibility == NGF_GROUP_PRIVATE && !$entity->getMember($account)) {
          return AccessResult::forbidden();
        }
        return GroupAccessResult::allowedIfHasGroupPermission($entity, $account, 'view group');

      case 'update':
        return GroupAccessResult::allowedIfHasGroupPermission($entity, $account, 'edit group');

      case 'delete':
        return GroupAccessResult::allowedIfHasGroupPermission($entity, $account, 'delete group');
    }

    return AccessResult::neutral();
  }

}
