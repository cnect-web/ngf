<?php

namespace Drupal\ngf_group\Entity\Decorator;

use Drupal\group\Entity\Group;
use Drupal\Core\Url;
use Drupal\Core\Link;

class NGFGroup {

  protected $group;

  /**
   * Constructor.
   */
  public function __construct(Group $group) {
    $this->group = $group;
  }

  /**
   * Magic method, propagates the parent class api to the current class.
   */
  public function __call($method, $args) {
    return call_user_func_array(array($this->group, $method), $args);
  }

  /**
   * Get group visibility setting.
   */
  public function getGroupVisibility() {
    $group = $this->group;
    $groupVisibility = NGF_GROUP_PUBLIC;
    if ($group->hasField('field_ngf_group_visibility')) {
      $groupVisibility = $group->get('field_ngf_group_visibility')->getString();
    }
    return $groupVisibility;
  }

  /**
   * Return true if the group is public.
   */
  public function isPublic() {
    $groupVisibility = $this->getGroupVisibility();
    return $groupVisibility == NGF_GROUP_PUBLIC;
  }

  /**
   * Return true if the group is private.
   */
  public function isPrivate() {
    $groupVisibility = $this->getGroupVisibility();
    return $groupVisibility == NGF_GROUP_PRIVATE;
  }

  /**
   * Return true if the group is secret.
   */
  public function isSecret() {
    $groupVisibility = $this->getGroupVisibility();
    return $groupVisibility == NGF_GROUP_SECRET;
  }

  /**
   * Return info about group members.
   */
  public function getMembersInfo() {
    $group = $this->group;

    $members_count_raw    = count($group->getMembers());
    $members_count_string = \Drupal::translation()->formatPlural($members_count_raw, '1 member', '@count members', array('@count' => $members_count_raw));
    $members_page_url     = Url::fromRoute('ngf_group.page.members', ['group' => $group->id()]);

    $members['count'] = $members_count_raw;
    $members['text']  = $members_count_string;
    $members['url']   = $members_page_url;

    return $members;
  }

  /**
   * Return a link to group members page.
   */
  public function getMembersLink() {
    $members = $this->getMembersInfo();
    return Link::fromTextAndUrl($members['text'], $members['url'])->toRenderable();
  }

  /**
   * Return info about group followers.
   */
  public function getFollowersInfo() {
    $group = $this->group;

    $flag_service = \Drupal::service('flag');
    $flaggings_count = \Drupal::service('flag.count')->getEntityFlagCounts($group);

    $followers_count_raw    = isset($flaggings_count['ngf_follow_group']) ? $flaggings_count['ngf_follow_group'] : 0;
    $followers_count_string = \Drupal::translation()->formatPlural($followers_count_raw, '1 follower', '@count followers', array('@count' => $followers_count_raw));
    $followers_page_url     = Url::fromRoute('ngf_group.page.followers', ['group' => $group->id()]);

    $followers['count'] = $followers_count_raw;
    $followers['text']  = $followers_count_string;
    $followers['url']   = $followers_page_url;

    return $followers;
  }

  /**
   * Return a link to group followers page.
   */
  public function getFollowersLink() {
    $followers = $this->getFollowersInfo();
    return Link::fromTextAndUrl($followers['text'], $followers['url'])->toRenderable();
  }

  /**
   * Returns group ops links.
   */
  public function getMembershipLinks() {
    $group = $this->group;
    $links = [];

    // Retrieve the operations from the installed content plugins.
    foreach ($group->getGroupType()->getInstalledContentPlugins() as $plugin) {
      if ($plugin->getPluginId() == 'group_membership') {
        $links += $plugin->getGroupOperations($group);
      }
    }

    if ($links) {
      // Allow modules to alter the collection of gathered links.
      \Drupal::moduleHandler()->alter('group_operations', $links, $group);

      // Sort the operations by weight.
      uasort($links, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

      foreach ($links as $key => $value) {
        $value['title'] = str_replace(' group', '', $value['title']);
        $variables[$key]['#title'] = $value['title'];
        $variables[$key]['#url'] = $value['url'];
        $variables[$key]['#type'] = 'link';
        $variables[$key]['#attributes']['class'][] = $key;
        //$variables[$key]['#attributes']['class'][] = "btn";
        //$variables[$key]['#attributes']['class'][] = "button-link";
      }

      // Remove the join group link for non-public groups.
      if (!$this->isPublic()) {
        unset($variables['group-join']);
      }
    }
    return $variables;
  }

  /**
   * Return the follow a group flag's render array.
   */
  public function getFollowFlag() {
    if (!\Drupal::currentUser()->isAnonymous()) {
      $group = $this->group;

      return [
        '#lazy_builder' => [
          'flag.link_builder:build',
          [
            $group->getEntityTypeId(),
            $group->id(),
            'ngf_follow_group',
          ],
        ],
        '#create_placeholder' => TRUE,
      ];
    }
  }

  /**
   * Return the group tabs.
   */
  public function getGroupTabs() {
    $group = $this->group;

    $links['publications'] = Link::fromTextAndUrl(t("Publications"), Url::fromRoute('ngf_group.page.publications', ['group' => $group->id()]));
    $links['events'] = Link::fromTextAndUrl(t("Events"), Url::fromRoute('ngf_group.page.events', ['group' => $group->id()]));
    $links['library'] = Link::fromTextAndUrl(t("Library"), Url::fromRoute('ngf_group.page.library', ['group' => $group->id()]));
    $links['shared-content'] = Link::fromTextAndUrl(t("Shared Content"), Url::fromRoute('ngf_group.page.shared', ['group' => $group->id()]));

    return $links;
  }

}
