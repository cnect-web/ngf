<?php

namespace Drupal\ngf_user_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\ngf_user_profile\Helper\UserHelper;
use Drupal\ngf_user_profile\Manager\UserListItemManager;
use Drupal\ngf_user_profile\Manager\UserListManager;
use Drupal\ngf_user_profile\Manager\FollowedUserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Class UserProfileController.
 */
class UserProfileController extends ControllerBase implements ContainerAwareInterface {

  use ContainerAwareTrait;

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
   * The user list item manager service.
   *
   * @var Drupal\ngf_user_profile\Manager\UserListItemManager
   */
  protected $userListItemManager;

  /**
   * The user list manager service.
   *
   * @var Drupal\ngf_user_profile\Manager\UserListManager
   */
  protected $userListManager;

  /**
   * The followed user service.
   *
   * @var Drupal\ngf_user_profile\Manager\FollowedUserManager
   */
  protected $followedUserManager;

  /**
   * UserProfileController constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param Drupal\ngf_user_profile\Manager\UserListItemManager $user_list_item_manager
   *   The user list item manager service.
   * @param Drupal\ngf_user_profile\Manager\UserListManager $user_list_manager
   *   The user list manager service.
   * @param Drupal\ngf_user_profile\Manager\FollowedUserManager $followed_user_manager
   *   The followed user manager service.
   */
  public function __construct(
    AccountInterface $current_user,
    MessengerInterface $messenger,
    UserListItemManager $user_list_item_manager,
    UserListManager $user_list_manager,
    FollowedUserManager $followed_user_manager
  ) {
    $this->currentUser = $current_user;
    $this->messenger = $messenger;
    $this->userListItemManager = $user_list_item_manager;
    $this->userListManager = $user_list_manager;
    $this->followedUserManager = $followed_user_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('messenger'),
      $container->get('ngf_user_profile.user_list_item_manager'),
      $container->get('ngf_user_profile.user_list_manager'),
      $container->get('ngf_user_profile.followed_user_manager')
    );
  }

  /**
   * Profile.
   *
   * @return string
   *   Return Hello string.
   */
  public function profile($username) {
    $user = user_load_by_name($username);
    return [
      '#type' => 'markup',
      '#title' => UserHelper::getUserFullName($user),
      '#markup' => '<a href="/profile/follow/' . $user->getUsername() . '">Follow</a>  - <a href="/profile/unfollow/' . $user->getUsername() . '">Unfollow</a>'
    ];
  }

  /**
   * Follow a passed user
   *
   * @param string $username
   *   User name
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirection response
   */
  public function follow($username) {
    $this->followedUserManager->follow($username);
    // Redirect back to a user profile.
    return $this->redirect('ngf_user_profile.profile', ['username' => $username]);
  }

  /**
   * Unfollow a passed user
   *
   * @param string $username
   *   User name
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirection response
   */
  public function unfollow($username) {
    $this->followedUserManager->unfollow($username);
    // Redirect back to a user profile.
    return $this->redirect('ngf_user_profile.profile', ['username' => $username]);
  }

  public function followed() {
    $followed_user_items = $this->followedUserManager->getList();
    $list = [];
    foreach ($followed_user_items as $followed_user_item) {
      $followed_user = $followed_user_item->getFollowedUser();
      $list[] = Link::fromTextAndUrl(UserHelper::getUserFullName($followed_user), Url::fromRoute('ngf_user_profile.unfollow', ['username' => $followed_user->getAccountName()]));
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $list,
    ];
  }

  public function userLists() {
    $lists = $this->userListManager->getList();

    $items = [];
    foreach ($lists as $list) {
      $items[] = Link::fromTextAndUrl($list->getName(), Url::fromRoute('ngf_user_profile.remove_user_list', ['list_id' => $list->id()]));
      $items[] = Link::fromTextAndUrl('items', Url::fromRoute('ngf_user_profile.user_list_items', ['list_id' => $list->id()]));
      $items[] = '-------------------------------';
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

  public function removeUserList($list_id) {
    $this->userListManager->removeListById([$list_id]);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_lists');
  }


  public function addUserlist($name) {
    $this->userListManager->add($name);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_lists');
  }

  public function removeUserlistItem($list_id, $username) {
    $this->userListItemManager->removeItemByListIdAndUsername($list_id, $username);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_list_items', ['list_id' => $list_id]);
  }

  public function addUserlistItem($list_id, $username) {
    $this->userListItemManager->remove($list_id, $username);
    return $this->redirect('ngf_user_profile.user_list_items', ['list_id' => $list_id]);
  }

  public function userListItems($list_id) {
    $list_items = $this->userListItemManager->getList($list_id);

    $items = [];
    foreach ($list_items as $list_item) {
      $items[] = Link::fromTextAndUrl(UserHelper::getUserFullName($list_item->getUser()), Url::fromRoute('ngf_user_profile.remove_user_list_item', ['username' => $list_item->getUser()->getAccountName(), 'list_id' => $list_id]));
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

}
