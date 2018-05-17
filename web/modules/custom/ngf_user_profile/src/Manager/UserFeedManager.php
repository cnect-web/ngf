<?php

namespace Drupal\ngf_user_profile\Manager;

use Drupal\Core\Session\AccountInterface;
use Drupal\group\Entity\Group;
use Drupal\user\Entity\User;

class UserFeedManager {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;
  protected $interests = [];
  protected $currentUserAccount = null;

  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  public function getItemsNumber() {
    return 10;
  }

  protected function getCurrentUserAccount() {
    if (empty($this->currentUserAccount)) {
      $this->currentUserAccount = User::load($this->currentUser->id());
    }
    return $this->currentUserAccount;
  }

  protected function getUserInterests() {
    if (empty($this->interests)) {
      $interests = [];
      $user = $this->getCurrentUserAccount();
      if (!empty($user)) {
        $interests = $user->get('field_ngf_interests');
      }
      foreach ($interests as $interest) {
        $value = $interest->getValue();
        if (!empty($value)) {
          $this->interests[$value['target_id']] = $value['target_id'];
        }
      }
    }
    return $this->interests;
  }

  public function getContent() {
    var_dump($this->getFollowedPublications());
    exit();
  }

  protected function getGroupIdsByInterests() {
    $query = \Drupal::entityQuery('group');
    $query->condition('type', 'ngf_discussion_group');
    $query->condition('field_ngf_interests', $this->getUserInterests(), 'IN');
    return $query->execute();
  }

  protected function getGroupIdsByMembership() {
    $group_ids = [];
    $grp_membership_service = \Drupal::service('group.membership_loader');
    $grps = $grp_membership_service->loadByUser($this->getCurrentUserAccount());
    foreach ($grps as $grp) {
      $group_ids[] = $grp->getGroup()->id();
    }
    return $group_ids;
  }

  protected function getGroupPublications() {
    $groups = Group::loadMultiple($this->getGroupIds());
    $publications = [];
    foreach ($groups as $group) {
      $group_content_items = $group->getContent('group_node:ngf_discussion');
      foreach ($group_content_items as $group_content_item) {
        $publications[$group_content_item->getEntity()->id()] = $group_content_item->getEntity();
      }
    }
  }

  protected function getPublicationsByInterests() {
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'ngf_discussion');
    $query->condition('field_ngf_interests', $this->getUserInterests(), 'IN');
    return $query->execute();
  }

  protected function getFollowedPublications() {
    $query = \Drupal::entityQuery('flagging');
    $query->condition('ngf_flag_id', 'ngf_follow_user');
    $query->condition('uid', $this->currentUser->id());
    return $query->execute();
  }

  protected function getGroupIds() {
    return array_unique(array_merge($this->getGroupIdsByInterests(), $this->getGroupIdsByMembership()));
  }

  public function getEvents() {

  }

  public function getPublications() {
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'ngf_discussion');
    $query->condition('field_ngf_interests', $this->getUserInterests(), 'IN');
    $query->condition('uid', $this->currentUser->id(), '<>');
    return $query->execute();
  }

}
