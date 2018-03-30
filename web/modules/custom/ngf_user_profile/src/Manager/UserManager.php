<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
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
   * UserProfileController constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(AccountInterface $current_user, MessengerInterface $messenger) {
    $this->currentUser = $current_user;
    $this->messenger = $messenger;

    $this->checkAccess();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('messenger')
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
