<?php

namespace Drupal\ngf_group\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'GroupTabsBlock' block.
 *
 * @Block(
 *   id = "group_tabs_block",
 *   admin_label = @Translation("Group Tabs block"),
 *   context = {
 *     "group" = @ContextDefinition(
 *       "entity:group",
 *       label = @Translation("Group"),
 *       required = TRUE
 *     ),
 *   }
 * )
 */
class GroupTabsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // This block varies per group type and per current user's group membership
    // permissions. Different group types could have different content plugins
    // enabled, influencing which group operations are available to them. The
    // active user's group permissions define which actions are accessible.
    //
    // We do not need to specify the current user or group as cache contexts
    // because, in essence, a group membership is a union of both.
    $build['#cache']['contexts'] = ['group.type', 'group_membership.roles.permissions'];

    // Of special note is the cache context 'group_membership.audience'. Where
    // the above cache contexts should suffice if everything is ran through the
    // permission system, group operations are an exception. Some operations
    // such as 'join' and 'leave' not only check for a permission, but also the
    // audience the user belongs to. I.e.: whether they're a 'member', an
    // 'outsider' or 'anonymous'.
    $build['#cache']['contexts'][] = 'group_membership.audience';

    /** @var \Drupal\group\Entity\GroupInterface $group */
    if (($group = $this->getContextValue('group')) && $group->id()) {
      $links = [];

      $options = ['absolute' => TRUE, 'attributes' => ['class' => 'this-class']];

      $links['publications'] = Link::fromTextAndUrl(t("Publications"), Url::fromRoute('ngf_group.page.publications', ['group' => $group->id()]));
      $links['events'] = Link::fromTextAndUrl(t("Events"), Url::fromRoute('ngf_group.page.events', ['group' => $group->id()]));
      $links['library'] = Link::fromTextAndUrl(t("Library"), Url::fromRoute('ngf_group.page.library', ['group' => $group->id()]));
      $links['shared-content'] = Link::fromTextAndUrl(t("Shared Content"), Url::fromRoute('ngf_group.page.shared', ['group' => $group->id()]));

      $build['tabs']['#title'] = '';
      $build['tabs']['#theme'] = 'item_list';
      $build['tabs']['#items'] = $links;
      $build['tabs']['#attributes']['class'][] = 'inline';
      $build['tabs']['#attributes']['class'][] = 'group';
      $build['tabs']['#attributes']['class'][] = 'tabs';
    }

    // If no group was found, cache the empty result on the route.
    return $build;
  }

}
