<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserManager {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The messenger service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * The messenger service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $flag;


  /**
   * UserProfileController constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\user\UserDataInterface $userData
   *   The user data service.
   */
  public function __construct(AccountInterface $current_user, MessengerInterface $messenger, UserDataInterface $userData) {
    $this->currentUser = $current_user;
    $this->messenger = $messenger;
    $this->userData = $userData;

    $this->checkAccess();
  }

  protected function checkAccess() {
    if ($this->currentUser->isAnonymous()) {
      throw new AccessDeniedHttpException();
    }
  }

  protected function addError($message) {
    $this->messenger->addMessage($message, MessengerInterface::TYPE_ERROR);
  }

  protected function addMessage($message) {
    $this->messenger->addMessage($message, MessengerInterface::TYPE_STATUS);
  }



}
