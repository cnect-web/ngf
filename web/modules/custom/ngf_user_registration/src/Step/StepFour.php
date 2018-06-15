<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepFourFinishButton;
use Drupal\ngf_user_registration\Button\StepFourPreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ngf_user_profile\UserSettingsManager;

/**
 * Class StepFour.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepFour extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_FOUR;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepFourPreviousButton(),
      new StepFourFinishButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {

    $form['setting_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => t('Private settings'),
    ];
    $settings = \Drupal::getContainer()->get('ngf_user_profile.user_settings_manager')->getList();
    foreach ($settings as $key => $setting) {
      $form['setting_wrapper'][$key] = [
        '#type' => 'checkbox',
        '#title' => $setting['title'],
        '#default_value' => $setting['default_value'],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return array_keys(\Drupal::getContainer()->get('ngf_user_profile.user_settings_manager')->getList());
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [];
  }

}
