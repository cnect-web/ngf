<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\ngf_user_profile\Entity\FollowedUser;
use Drupal\ngf_user_profile\Helper\UserHelper;

class FollowedUserManager extends UserManager {

  public function getEntityType() {
    return 'ngf_followed_user';
  }

  /**
   * Add passed user to the list of followed users.
   *
   * @param string $username
   *   Username.
   */
  public function follow($username) {
    $user = user_load_by_name($username);

    if (empty($user)) {
      $this->addError(t('User is not found.'));
    }
    else if ($this->currentUser->id() === $user->id()) {
      $this->addError(t('You cannot follow yourself'));
    } else {
      $users = \Drupal::entityQuery($this->getEntityType())
        ->condition('user_id', $this->currentUser->id())
        ->condition('followed_user_id', $user->id())
        ->execute();

      $user_name = UserHelper::getUserFullName($user);

      if (empty($users)) {
        $followed_user = FollowedUser::create([
          'user_id' => $this->currentUser->id(),
          'followed_user_id' => $user->id(),
        ]);
        $followed_user->save();

        if ($followed_user) {
          $this->addMessage(t('You are now following @username', ['@username' => $user_name]));
        } else {
          $this->addError(t('Something went wrong, impossible to follow @username', ['@username' => $user_name]));
        }
      }
      else {
        $this->addError(t('You are already following @username', ['@username' => $user_name]));
      }

    }
  }

  /**
   * Remove passed user to the list of followed users.
   *
   * @param string $username
   *   Username.
   */
  public function unfollow($username) {
    $user = user_load_by_name($username);

    if (empty($user)) {
      $this->messenger->addMessage(t('User is not found.'), MessengerInterface::TYPE_ERROR);
    }
    else {

      $user_name = UserHelper::getUserFullName($user);

      $users = \Drupal::entityQuery($this->getEntityType())
        ->condition('user_id', $this->currentUser->id())
        ->condition('followed_user_id', $user->id())
        ->execute();

      $this->removeItems($users);
      $this->messenger->addMessage(t('You are not following anymore @username', ['@username' => $user_name]), MessengerInterface::TYPE_STATUS);
    }
  }

  public function getList() {
    $users = \Drupal::entityQuery($this->getEntityType())
      ->condition('user_id', $this->currentUser->id())
      ->execute();

    return FollowedUser::loadMultiple($users);
  }

}
