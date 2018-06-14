<?php

namespace Drupal\ngf_user_registration\Button;

use Drupal\ngf_user_registration\Step\StepsEnum;

/**
 * Class StepThreeNextButton.
 *
 * @package Drupal\ngf_user_registration\Button
 */
class StepThreeNextButton extends BaseButton {

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    return 'finish';
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'submit',
      '#value' => t('Finish!'),
      '#goto_step' => StepsEnum::STEP_FOUR,
      '#submit_handler' => 'submitValues',
    ];
  }

}
