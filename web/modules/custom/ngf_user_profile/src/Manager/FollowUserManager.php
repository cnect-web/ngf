<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\ngf_user_profile\Helper\UserHelper;

class FollowUserManager extends UserManager {

  /**
   * The messenger service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * Add passed user to the list of followed users.
   *
   * @param string $username
   *   Username.
   */
  public function follow($username) {
    $flag_id = 'bookmark';

    $flag_service = \Drupal::service('flag');
    $flag = $flag_service->getFlagById($flag_id);
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
