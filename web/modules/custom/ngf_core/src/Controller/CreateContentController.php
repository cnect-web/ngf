<?php

namespace Drupal\ngf_core\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\views\Views;
use Drupal\Core\Url;

/**
 * Front page controller.
 */
class CreateContentController extends ControllerBase {
  /**
   * Returns a render-able array for a test page.
   */
  public function content() {

    $user = \Drupal::currentUser();

    $contentTypes = \Drupal::service('entity.manager')
      ->getStorage('node_type')
      ->loadMultiple();

    foreach ($contentTypes as $contentType) {
      if ($user->hasPermission("create {$contentType->id()} content")) {
        $url = new Url('node.add', ['node_type' => $contentType->id()]);
        $links[] = [
          'title' => t("Create {$contentType->label()}"),
          'type' => 'link',
          'url' => $url,
          'attributes' => [
            'class' => [
              'btn',
              'btn--green'
            ]
          ]
        ];
      }
    }

    $render = [
      '#theme' => 'links',
      '#links' => $links,
      '#attributes' => [
        'class' => [
          'add-content-links',
        ]
      ]
    ];

    return $render;
  }
}
