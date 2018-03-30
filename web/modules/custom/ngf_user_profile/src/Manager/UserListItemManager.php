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
          $this->addMessage(t('You now follow @username', ['@']));
        } else {
          $this->addError(t('Something went wrong, impossible to add user @username'));
        }
      }
      else {
        $this->addError(t('You already follow @username'));
      }

    }
  }

  public function removeItemByListId($list_id) {
    $list_item_ids = \Drupal::entityQuery('ngf_user_list_item')
      ->condition('user_id', $this->currentUser->id())
      ->condition('list_id', $list_id)
      ->execute();

    $this->removeItems($list_item_ids);

    $this->addMessage(t('List items have been removed'));
  }

}
