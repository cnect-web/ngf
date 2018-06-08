<?php

namespace Drupal\ngf_user_registration\Step;

/**
 * Class StepFinalize.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepFinalize extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FINALIZE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements() {

    $form['completed'] = [
      '#markup' => t('You have completed the wizard, yeah!'),
    ];

    return $form;
  }

}
