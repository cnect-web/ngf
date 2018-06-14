<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepThreeFinishButton;
use Drupal\ngf_user_registration\Button\StepThreePreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRegex;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

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
  public function buildStepFormElements(FormStateInterface $form_state) {

    $form['interests_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'interests-wrapper'],
      '#tree' => TRUE,
    ];

//    $form['#tree'] = TRUE;
    $interests_items = $this->getValue('interests_items', $form_state->get('interests_items'));
    if (empty($interests_items)) {
      $interests_items = 1;
      $form_state->set('interests_items', $interests_items);
    }

    for ($i = 0; $i < $interests_items; $i++) {
      $form['interests_wrapper']['interests'][$i] = [
        '#type' => 'entity_autocomplete',
        '#target_type' => 'taxonomy_term',
        '#selection_settings' => [
          'include_anonymous' => FALSE,
          'target_bundles' => ['ngf_interests'],
        ],
//        '#default_value' => $this->getValues()['interests'][$i] ?? NULL,
      ];

      ksm($this->getValues());
      $interest = $this->getValue('interests_wrapper')['interests'][$i] ?? NULL;
      if (!empty($interest) && $term = Term::Load($interest)) {
        $form['interests_wrapper']['interests'][$i]['#default_value'] = $term;
      }
    }

    $form['interests_wrapper']['add_item'] = [
      '#type' => 'submit',
      '#name' => 'add_item',
      '#value' => t('Add interest'),
      '#submit' => [[get_class($this), 'interestsAddItem']],
      '#ajax' => [
        'callback' => [get_class($this), 'getInterestField'],
        'wrapper' => 'interests-wrapper',
      ],
    ];
    $form_state->setCached(FALSE);

    return $form;
  }

  /**
   * Ajax Callback for the form.
   *
   * @param array $form
   *   The form being passed in
   * @param array $form_state
   *   The form state
   *
   * @return array
   *   The form element we are changing via ajax
   */
  public static function getInterestField(&$form, FormStateInterface $form_state) {
    return $form['wrapper']['interests_wrapper'];
  }

  /**
   * Functionality for our ajax callback.
   *
   * @param array $form
   *   The form being passed in
   * @param array $form_state
   *   The form state, passed by reference so we can modify
   */
  public static function interestsAddItem(&$form, FormStateInterface $form_state) {
    $interests_items = $form_state->get('interests_items');
    $form_state->set('interests_items', ++$interests_items);
    $form_state->setRebuild();

  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'interests_wrapper',
    ];
  }
}
