<?php

namespace Drupal\ngf_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\group\Cache\Context\GroupCacheContext;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Provides a 'CreateContent' Block.
 *
 * @Block(
 *   id = "create_content_block",
 *   admin_label = @Translation("Create Content Block"),
 *   category = @Translation("NGF Core"),
  *  context = {
 *     "group" = @ContextDefinition("entity:group", required = FALSE)
 *   }
 * )
 */
class CreateContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = \Drupal::currentUser();
    $render = [];
    if ($user->isAuthenticated()) {

      $build['#cache']['contexts'] = ['group.type', 'group_membership.roles.permissions'];
      $build['#cache']['contexts'][] = 'group_membership.audience';

      $url = (($group = $this->getContextValue('group')) && $group->id())
        ? Url::fromRoute('ngf_core.create_group_content', ['group' => $group->id()])
        : Url::fromRoute('ngf_core.create_content');

      $obj = JSON::Decode('{"width":400,"height":300}');

      $render['button'] = [
        '#type' => 'link',
        '#title' => 'Create new ...',
        '#url' => $url,
        '#attributes' => [
          'class' => [
            'use-ajax',
            'button',
          ],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => JSON::Encode($obj),
        ],
      ];
    }
    return $render;
  }

}
