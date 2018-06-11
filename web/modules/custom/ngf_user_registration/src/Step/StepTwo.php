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
    $form['interests'] = [
      '#type' => 'checkboxes',
      '#title' => t('Nice to meet you! So, what are you interests?'),
      '#options' => [1 => 'interest 1', 2 => 'interest 2', 3 => 'interest 3'],
      '#default_value' => isset($this->getValues()['interests']) ? $this->getValues()['interests'] : [],
      '#required' => FALSE,
    ];

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
        new ValidatorRequired("It would be a lot easier for me if you could fill out some of your interests."),
      ],
    ];
  }

}
