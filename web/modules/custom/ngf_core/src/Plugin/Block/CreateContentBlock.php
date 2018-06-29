<?php

namespace Drupal\ngf_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;

/**
 * Provides a 'CreateContent' Block.
 *
 * @Block(
 *   id = "create_content_block",
 *   admin_label = @Translation("Create Content Block"),
 *   category = @Translation("NGF Core"),
 * )
 */
class CreateContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $url = Url::fromRoute('ngf_core.create_content');
    $obj = JSON::Decode('{"width":400,"height":300}');

    $render['button'] = [
      '#type' => 'link',
      '#title' => 'Create Content',
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

    return $render;
  }

}
