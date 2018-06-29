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


    ksm($login_block);


    $submit_copy = $login_block['user_login_form']['actions']['submit'];
    $actions_copy = $login_block['user_login_form']['actions'];
    unset($login_block['user_login_form']['actions']['submit']);
    unset($login_block['user_links']['#items']['create_account']);
    unset($login_block['user_login_form']['actions']);

    $login_block['user_login_form']['login_links_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'clearfix'
        ]
      ],
      '#weight' => 10,
    ];
    $login_block['user_login_form']['login_links_wrapper']['actions'] = $actions_copy;
    $login_block['user_login_form']['login_links_wrapper']['actions']['submit'] = $submit_copy;

    $login_block['user_login_form']['login_links_wrapper']['reset_password_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'btn-list--left',
          'btn-list--byside'
        ]
      ]
    ];
    $login_block['user_login_form']['login_links_wrapper']['reset_password_wrapper']['p_wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
    ];

    $reset_password_copy = $login_block['user_links']['#items']['request_password'];
    $login_block['user_login_form']['login_links_wrapper']['reset_password_wrapper']['p_wrapper']['reset_password'] = $reset_password_copy;

    $login_block['user_login_form']['wrapper-messages'] = [
      '#markup' => '<div id="messages-wrapper" data-drupal-selector="edit-wrapper-messages" class="js-form-wrapper form-wrapper"></div>',
      '#weight' => -2,
    ];

    $login_block['user_login_form']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => t("Login"),
      '#suffix' => '<br>',
      '#weight' => -1,
    ];


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
      '#value' => t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent ullamcorper faucibus ipsum, quis lobortis justo. Donec blandit nunc sed elementum euismod. Nulla venenatis nec odio vitae tincidunt. Donec et enim non orci posuere ultrices quis sit amet mauris. Nunc blandit sapien quis tincidunt imperdiet.'),
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
