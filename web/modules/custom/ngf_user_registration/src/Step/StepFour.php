<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepFourFinishButton;
use Drupal\ngf_user_registration\Button\StepFourPreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ngf_user_profile\UserSettingsManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StepFour.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepFour extends BaseStep {

  protected $userSettings = NULL;

  /**
   * StepFour constructor.
   *
   * @param Drupal\ngf_user_profile\Manager\UserSettingsManager $user_settings
   *   The user settings.
   */
  public function __construct() {
    $this->userSettings = \Drupal::getContainer()->get('ngf_user_profile.user_settings_manager');
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(

    );
  }

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
    $settings = $this->userSettings->getList();
    foreach ($settings as $key => $setting) {
      $form['setting_wrapper'][$key] = [
        '#type' => 'checkbox',
        '#title' => $setting['title'],
        '#default_value' => $setting['default_value'],
      ];
    }

    $form_state->setCached(FALSE);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return array_keys($this->userSettings->getList());
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [];
  }

}
