<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepTwoNextButton;
use Drupal\ngf_user_registration\Button\StepTwoPreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;

/**
 * Class StepTwo.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepTwo extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_TWO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepTwoPreviousButton(),
      new StepTwoNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements() {
    $form['city'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'target_bundles' => array('ngf_cities'),
      ],
      '#default_value' => !empty($this->getValues()['city']) ? $this->getValues()['city'] : NULL,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'city',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'city' => [
//        new ValidatorRequired("It would be a lot easier for me if you could fill out some of your interests."),
      ],
    ];
  }

}
