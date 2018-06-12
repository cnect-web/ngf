<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepThreeFinishButton;
use Drupal\ngf_user_registration\Button\StepThreePreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRegex;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;

/**
 * Class StepThree.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepThree extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_THREE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepThreePreviousButton(),
      new StepThreeFinishButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements() {
    $form['interests'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'target_bundles' => array('ngf_interests'),
      ],
      '#default_value' => !empty($this->getValues()['interests']) ? $this->getValues()['interests'] : NULL,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'interests',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'interests' => [
      ],
    ];
  }

}
