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
class FrontPageController extends ControllerBase {
  /**
   * Returns a render-able array for a test page.
   */
  public function anonymousFrontPage() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $plugin_block = $block_manager->createInstance('user_login_block', []);

    $login_block = $plugin_block->build();
    $login_block['user_links']['#access'] = FALSE;

    $render['login_form'] = $login_block;

    $render['message_wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
    ];

    $render['message_wrapper']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => t('Not registred yet ?'),
    ];

    $render['message_wrapper']['body'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => t('asdsdds Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ullamcorper faucibus ipsum, quis lobortis justo. Donec blandit nunc sed elementum euismod. Nulla venenatis nec odio vitae tincidunt. Donec et enim non orci posuere ultrices quis sit amet mauris. Nunc blandit sapien quis tincidunt imperdiet.'),
    ];

    $render['register_links_wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'btn-list--center',
        ]
      ]
    ];

    $url = Url::fromRoute('ngf_user_registration');
    $render['register_links_wrapper']['register_link'] = [
      '#type' => 'link',
      '#title' => t('Register'),
      '#url' => $url,
      '#attributes' => [
        'class' => [
          'btn btn--blue btn--large',
        ]
      ]
    ];

    return $render;
  }

  public function  authenticatedFrontPage() {
    return ['#markup' => ''];
  }

  public function content() {
    if (\Drupal::currentUser()->isAnonymous()) {
      return $this->anonymousFrontPage();
    }
    else {
      return $this->authenticatedFrontPage();
    }
  }

}