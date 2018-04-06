<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\ngf_user_profile\Entity\UserList;

class UserListManager extends UserManager {

  public function getEntityType() {
    return 'ngf_user_list';
  }

  public function add($name) {
    if (empty($name)) {
      $this->addError(t('Name of the list is empty.'));
    }
    else {
      $lists = \Drupal::entityQuery($this->getEntityType())
        ->condition('user_id', $this->currentUser->id())
        ->condition('name', $name)
        ->execute();

      if (empty($lists)) {
        $user_list_item = UserList::create([
          'user_id' => $this->currentUser->id(),
          'name' => $name,
        ]);
        $user_list_item->save();

        if ($user_list_item) {
          $this->addMessage(t('@name has been added', ['@name' => $name]));
        } else {
          $this->addError(t('Something went wrong, impossible to add @name list', ['@name' => $name]));
        }
      }
      else {
        $this->addError(t('You already have @name list', ['@name' => $name]));
      }
    }
  }

  public function getList() {
    $list_ids = \Drupal::entityQuery($this->getEntityType())
      ->condition('user_id', $this->currentUser->id())
      ->execute();

    return UserList::loadMultiple($list_ids);
  }

  public function removeListById($ids = []) {
    $list_ids = \Drupal::entityQuery($this->getEntityType())
      ->condition('user_id', $this->currentUser->id())
      ->condition('id', $ids)
      ->execute();
    $user_lists = UserList::loadMultiple($list_ids);
    if (!empty($user_lists)) {
      $names = [];
      foreach ($user_lists as $user_list) {
        $names[] = $user_list->getName();
        // Remove related list items.
        $user_list_item_manager = \Drupal::service('ngf_user_profile.user_list_item_manager');
        $user_list_item_manager->removeItemByListId($user_list->id());
      }

      $this->removeItems($ids);

      $this->addMessage(t('Lists "@names" have been removed', ['@names' => implode(', ', $names)]));
    }
  }

  protected function removeItems($item_ids = []) {
    $storage_handler = \Drupal::entityTypeManager()->getStorage($this->getEntityType());
    $entities = $storage_handler->loadMultiple($item_ids);
    if (!empty($entities)) {
      $storage_handler->delete($entities);
    }
  }


}

