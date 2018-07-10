<?php

namespace Drupal\ngf_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Image\Entity\ImageStyle;

/**
 * Provides a 'User Account' Block.
 *
 * @Block(
 *   id = "UserAccountBlock",
 *   admin_label = @Translation("User Account Block"),
 *   category = @Translation("User Account Block"),
 * )
 */
class UserAccountBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $plugin_user_account_block = $block_manager->createInstance('system_menu_block:account', []);
    $user_account_block = $plugin_user_account_block->build();

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $variables = array(
      'style_name' => 'thumbnail',
      'uri' => $user->user_picture->entity->getFileUri(),
    );

    // The image.factory service will check if our image is valid.
    $image = \Drupal::service('image.factory')->get($user->user_picture->entity->getFileUri());
    if ($image->isValid()) {
      $variables['width'] = $image->getWidth();
      $variables['height'] = $image->getHeight();
    }
    else {
      $variables['width'] = $variables['height'] = NULL;
    }

    $logo_render_array = [
      '#theme' => 'image_style',
      '#width' => $variables['width'],
      '#height' => $variables['height'],
      '#style_name' => $variables['style_name'],
      '#uri' => $variables['uri'],
    ];


    return [
      '#theme' => 'login_account_block',
      '#user_picture' => render($logo_render_array),
      '#user_name' => $user->getUsername(),
      '#user_profile_link' => '#',
      '#user_manage_network_link' => '#',
      '#user_logout_link' => '/user/logout',
    ];
  }
}