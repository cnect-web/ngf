<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepOneNextButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\user\RegisterForm;

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
  public function buildStepFormElements() {
    $entity_manager = \Drupal::service('entity.manager');
    $entity_form_builder = \Drupal::service('entity.form_builder');
    $account = $entity_manager->getStorage('user') ->create([]);
    $register_form = $entity_form_builder->getForm($account, 'register');

    $form['first_name'] = $register_form['field_ngf_first_name'];
    $form['last_name'] = $register_form['field_ngf_last_name'];
    $form['username'] = $register_form['account']['name'];
    $form['password'] = $register_form['account']['pass'];
    $form['email'] = $register_form['account']['mail'];
//      [
//      '#type' => 'textfield',
//      '#title' => t('First name'),
//      '#required' => TRUE,
//      '#default_value' => !empty($this->getValues()['first_name']) ? $this->getValues()['first_name'] : NULL,
//    ];
//    $form['last_name'] = [
//      '#type' => 'textfield',
//      '#title' => t('Last name'),
//      '#required' => TRUE,
//      '#default_value' => !empty($this->getValues()['last_name']) ? $this->getValues()['last_name'] : NULL,
//    ];
//    $form['username'] = [
//      '#type' => 'textfield',
//      '#title' => t('Username'),
//      '#required' => TRUE,
//      '#default_value' => !empty($this->getValues()['username']) ? $this->getValues()['username'] : NULL,
//    ];
//    $form['password'] = [
//      '#type' => 'password_confirm',
//      '#title' => t('Password'),
//      '#required' => TRUE,
//      '#default_value' => !empty($this->getValues()['password']) ? $this->getValues()['password'] : [],
//    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'first_name',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'name' => [
        new ValidatorRequired("Hey stranger, please tell me your name. I would like to get to know you."),
      ],
    ];
  }

}

