<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\ngf_user_profile\Entity\UserList;
use Drupal\ngf_user_profile\Entity\UserListItem;
use Drupal\ngf_user_profile\Helper\UserHelper;

class UserListItemManager extends UserManager {

  public function getEntityType() {
    return 'ngf_user_list_item';
  }

  public function add($list_id, $username) {
    $list = UserList::load($list_id);
    $user = user_load_by_name($username);

    if (empty($user)) {
      $this->addError(t('User is not found.'));
    } elseif (empty($list)) {
      $this->addError(t('List is not found.'));
    } else {
      $user_list_items = \Drupal::entityQuery('ngf_user_list_item')
        ->condition('user_id', $this->currentUser->id())
        ->condition('list_user_id', $user->id())
        ->condition('list_id', $list->id())
        ->execute();

      if (empty($user_list_items)) {
        $user_list_item = UserListItem::create([
          'user_id' => $this->currentUser->id(),
          'list_user_id' => $user->id(),
          'list_id' => $list->id()
        ]);
        $user_list_item->save();

        if ($user_list_item) {
          $this->addMessage(t('User @username has been added to the list ', ['@username' => UserHelper::getUserFullName($user)]));
        } else {
          $this->addError(t('Something went wrong, impossible to add user @username', ['@username' => UserHelper::getUserFullName($user)]));
        }
      }
      else {
        $this->addError(t('You have user @username in the list', ['@username' => UserHelper::getUserFullName($user)]));
      }

    }
  }

  public function removeItemByListId($list_id) {
    $list_item_ids = \Drupal::entityQuery('ngf_user_list_item')
      ->condition('user_id', $this->currentUser->id())
      ->condition('list_id', $list_id)
      ->execute();

    $this->removeItems($list_item_ids);

    $this->addMessage(t('Users have been removed from the list'));
  }

  public function removeItemByListIdAndUsername($list_id, $username) {
    if ($user = user_load_by_name($username)) {
      $list_item_ids = \Drupal::entityQuery('ngf_user_list_item')
        ->condition('user_id', $this->currentUser->id())
        ->condition('list_id', $list_id)
        ->condition('list_user_id', $user->id())
        ->execute();

      $this->removeItems($list_item_ids);

      $this->addMessage(t('User has been removed from the list'));
    } else {
      $this->addError(t('User is not found'));
    }
  }


  public function getList($list_id) {
    $list_ids = \Drupal::entityQuery($this->getEntityType())
      ->condition('user_id', $this->currentUser->id())
      ->condition('list_id', $list_id)
      ->execute();

    return UserListItem::loadMultiple($list_ids);
  }

}
