<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepOneNextButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\ngf_user_registration\Validator\ValidatorUsername;
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
//    $entity_manager = \Drupal::service('entity.manager');
//    $entity_form_builder = \Drupal::service('entity.form_builder');
//    $account = $entity_manager->getStorage('user') ->create([]);
//    $register_form = $entity_form_builder->getForm($account, 'register');
//
////    var_dump($register_form['field_ngf_first_name']['weight']);
////    var_dump($register_form['field_ngf_last_name']['weight']);
//
//    $form['first_name'] = $register_form['field_ngf_first_name'];
//    $form['last_name'] = $register_form['field_ngf_last_name'];
//    $form['username'] = $register_form['account']['name'];
//    $form['email'] = $register_form['account']['mail'];
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#required' => TRUE,
      '#default_value' => !empty($this->getValues()['first_name']) ? $this->getValues()['first_name'] : NULL,
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => t('Last name'),
      '#required' => TRUE,
      '#default_value' => !empty($this->getValues()['last_name']) ? $this->getValues()['last_name'] : NULL,
    ];
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => t('Username'),
      '#required' => TRUE,
      '#default_value' => !empty($this->getValues()['username']) ? $this->getValues()['username'] : NULL,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Email'),
      '#required' => TRUE,
      '#default_value' => !empty($this->getValues()['email']) ? $this->getValues()['email'] : NULL,
    ];
    $form['terms'] = [
      '#type' => 'checkbox',
      '#title' => t('By clicking Register, you agree to our Terms and that you have read our Data Use Policy, including our Cookies Use'),
      '#required' => TRUE,
      '#default_value' => !empty($this->getValues()['terms']) ? $this->getValues()['terms'] : NULL,
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

