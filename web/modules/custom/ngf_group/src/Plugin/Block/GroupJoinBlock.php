<?php

namespace Drupal\ngf_group\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'GroupJoinBlock' block.
 *
 * @Block(
 *   id = "group_join_block",
 *   admin_label = @Translation("Group Join block"),
 *   context = {
 *     "group" = @ContextDefinition(
 *       "entity:group",
 *       label = @Translation("Group"),
 *       required = TRUE
 *     ),
 *   }
 * )
 */
class GroupJoinBlock extends BlockBase {

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

      // Retrieve the operations from the installed content plugins.
      foreach ($group->getGroupType()->getInstalledContentPlugins() as $plugin) {
        if ($plugin->getPluginId() == 'group_membership') {
          /** @var \Drupal\group\Plugin\GroupContentEnablerInterface $plugin */
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
          $items[$key]['#title'] = $value['title'];
          $items[$key]['#url'] = $value['url'];
          $items[$key]['#type'] = 'link';
          $items[$key]['#attributes']['class'] = [
            $key,
            'btn',
            'button-link',
          ];
        }

        // Follow flag dummy button.
        // @todo: replace with real flag.
        $items['group-follow'] = Link::fromTextAndUrl(t('Follow'), Url::fromUri('http://ngf.dev.europa.eu/'))->toRenderable();
        $items['group-follow']['#attributes']['class'] = [
          'follow-group',
          'btn',
          'button-link',
        ];

        // Create an operations element with all of the links.
        $build['#title'] = '';
        $build['#theme'] = 'item_list';
        $build['#items'] = $items;
      }
    }

    // If no group was found, cache the empty result on the route.
    return $build;
  }

}
