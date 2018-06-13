<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepOneNextButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\ngf_user_registration\Validator\ValidatorUsername;
use Drupal\user\RegisterForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepOne.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepOne extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_ONE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepOneNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#required' => TRUE,
      '#default_value' => $this->getValues()['first_name'] ?? NULL,
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => t('Last name'),
      '#required' => TRUE,
      '#default_value' => $this->getValues()['last_name'] ?? NULL,
    ];
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => t('Username'),
      '#required' => TRUE,
      '#default_value' => $this->getValues()['username'] ?? NULL,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#required' => TRUE,
      '#default_value' => $this->getValues()['email'] ?? NULL,
    ];
    $form['terms'] = [
      '#type' => 'checkbox',
      '#title' => t('By clicking Register, you agree to our Terms and that you have read our Data Use Policy, including our Cookies Use'),
      '#required' => TRUE,
      '#default_value' => $this->getValues()['terms'] ?? NULL,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'first_name',
      'last_name',
      'username',
      'email',
      'terms',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'username' => [
        new ValidatorUsername('Only several special characters are allowed, including space, ., -, \, _, @.  ')
      ],
    ];
  }

}

