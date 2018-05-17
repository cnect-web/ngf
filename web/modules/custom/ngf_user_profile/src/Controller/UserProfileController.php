<?php

namespace Drupal\ngf_user_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ngf_user_profile\Helper\UserHelper;
use Drupal\ngf_user_profile\Manager\UserFeedManager;
use Drupal\ngf_user_profile\Manager\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Class UserProfileController.
 */
class UserProfileController extends ControllerBase implements ContainerAwareInterface {

  use ContainerAwareTrait;

  /**
   * The user list item manager service.
   *
   * @var Drupal\ngf_user_profile\Manager\userManager
   */
  protected $userManager;

  /**
   * The user feed manager service.
   *
   * @var Drupal\ngf_user_profile\Manager\userFeedManager
   */
  protected $userFeedManager;

  /**
   * UserProfileController constructor.
   *
   * @param Drupal\ngf_user_profile\Manager\UserManager $user_manager
   *   The user manager.
   * @param Drupal\ngf_user_profile\Manager\UserFeedManager $user_feed_manager
   *   The user manager.
   */
  public function __construct(
    userManager $user_manager,
    UserFeedManager $user_feed_manager
  ) {
    $this->userManager = $user_manager;
    $this->userFeedManager = $user_feed_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ngf_user_profile.user_manager'),
      $container->get('ngf_user_profile.user_feed_manager')
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
   * Follow a user
   *
   * @param string $username
   *   User name
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirection response
   */
  public function follow($username) {
    $this->userManager->follow($username);
    // Redirect back to a user profile.
    return $this->redirect('ngf_user_profile.profile', ['username' => $username]);
  }

  /**
   * Unfollow a user
   *
   * @param string $username
   *   User name
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirection response
   */
  public function unfollow($username) {
    $this->userManager->unfollow($username);
    // Redirect back to a user profile.
    return $this->redirect('ngf_user_profile.profile', ['username' => $username]);
  }

  public function followed() {
    $followed_user_items = $this->userManager->getFollowedList();
    $list = [];
    foreach ($followed_user_items as $user_item) {
        $list[] = $this->entityTypeManager()->getViewBuilder($user_item->getEntityTypeId())->view($user_item);
        $list[] = Link::fromTextAndUrl(UserHelper::getUserFullName($user_item), Url::fromRoute('ngf_user_profile.unfollow', ['username' => $user_item->getAccountName()]));
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $list,
    ];
  }

  public function userLists() {
    $lists = $this->userManager->getUserLists();
    $items = [];
    foreach ($lists as $list) {
      $items[] = Link::fromTextAndUrl('Remove list ' . $list->getName(), Url::fromRoute('ngf_user_profile.remove_user_list', ['list_id' => $list->id()]));
      $items[] = Link::fromTextAndUrl('items', Url::fromRoute('ngf_user_profile.user_list_items', ['list_id' => $list->id()]));
      $items[] = '-------------------------------';
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

  public function removeUserList($list_id) {
    $this->userManager->removeUserList($list_id);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_lists');
  }


  public function addUserList($name) {
    $this->userManager->addUserList($name);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_lists');
  }

  public function removeUserListItem($list_id, $username) {
    $this->userManager->removeUserListItem($list_id, $username);
    // Redirect back to a user lists.
    return $this->redirect('ngf_user_profile.user_list_items', ['list_id' => $list_id]);
  }

  public function addUserListItem($list_id, $username) {
    $this->userManager->addUserListItem($list_id, $username);
    return $this->redirect('ngf_user_profile.user_list_items', ['list_id' => $list_id]);
  }

  public function userListItems($list_id) {
    $list_items = $this->userManager->getUserListItems($list_id);
    $items = [];
    foreach ($list_items as $list_item) {
      $items[] = Link::fromTextAndUrl(UserHelper::getUserFullName($list_item) . ' - ' . $list_item->id(), Url::fromRoute('ngf_user_profile.remove_user_list_item', ['username' => $list_item->getAccountName(), 'list_id' => $list_id]));
    }

    return $render = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

  public function notifications() {
    $notification_manager = $this->container->get('ngf_user_profile.notification_manager');
    $messages = $notification_manager->getUserNotifications();
    $items = [];
    foreach ($messages as $message) {
      $text = $message->getText();
      $items[] =  \array_shift($text) . ' - <a href="/profile/notification/markasread/' . $message->id() . '">Mark as read</a> - <a href="/profile/notification/remove/' . $message->id() . '">Delete</a><br>';
    }

    return  [
      '#markup' => implode(" - ", $items)
    ];
  }

  public function markAsRead($message_id) {
    $notification_manager = $this->container->get('ngf_user_profile.notification_manager');
    $notification_manager->markNotificationAsRead($message_id);
    return $this->redirect('ngf_user_profile.user_notifications');
  }

  public function removeNotification($message_id) {
    $notification_manager = $this->container->get('ngf_user_profile.notification_manager');
    $notification_manager->removeNotification($message_id);
    return $this->redirect('ngf_user_profile.user_notifications');
  }

  public function feed() {
    $this->userFeedManager->getContent();
    exit('feed');
//    $arr = [];
//    for($i = 1; $i <= 100; $i++) {
//      $arr[] = $i;
//    }
//
//    $page = pager_find_page();
//    $num_per_page = 10;
//    $offset = $num_per_page * $page;
//    $result = array_slice($arr, $offset, $num_per_page);
//
//    // Now that we have the total number of results, initialize the pager.
//    pager_default_initialize(count($arr), $num_per_page);
//
//    // Create a render array with the search results.
//    $render = [];
//    $render[] = [
//      '#theme' => 'item_list',
//      '#items' => $result,
//    ];
//
//    // Finally, add the pager to the render array, and return.
//    $render[] = [
//      '#type' => 'pager',
//    ];
//    return $render;

  }

}
