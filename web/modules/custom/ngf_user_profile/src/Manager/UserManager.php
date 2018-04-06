<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class UserManager implements ContainerInjectionInterface {
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

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('messenger'),
      $container->get('user.data')
    );
  }

  protected function checkAccess() {
    if ($this->currentUser->isAnonymous()) {
      throw new AccessDeniedHttpException();
    }
  }

  protected function removeItems($item_ids = []) {
    $storage_handler = \Drupal::entityTypeManager()->getStorage($this->getEntityType());
    $entities = $storage_handler->loadMultiple($item_ids);
    if (!empty($entities)) {
      $storage_handler->delete($entities);
    }
  }


  protected function addError($message) {
    $this->messenger->addMessage($message, MessengerInterface::TYPE_ERROR);
  }

  protected function addMessage($message) {
    $this->messenger->addMessage($message, MessengerInterface::TYPE_STATUS);
  }

  public abstract function getEntityType();

}
