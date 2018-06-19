<?php

namespace Drupal\ngf_user_profile\Form;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ngf_user_registration\Manager\StepManager;
use Drupal\ngf_user_registration\Step\StepsEnum;
use Drupal\redirect\Entity\Redirect;
use Drupal\user\RoleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ngf_user_profile\Manager\UserSettingsManager;
use Drupal\user\UserData;

/**
 * Provides multi step ajax example form.
 *
 * @package Drupal\ngf_user_registration\Form
 */
class UserPrivateSettingsForm extends FormBase {
  use StringTranslationTrait;

  /**
   * @var UserSettingsManager $userSettingsManager
   */
  protected $userSettingsManager;

  /**
   * @var UserData $userData
   */
  protected $userData;

  /**
   * Class constructor.
   */
  public function __construct(UserSettingsManager $userSettingsManager, UserData $userData) {
    $this->userSettingsManager = $userSettingsManager;
    $this->userData = $userData;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ngf_user_profile.user_settings_manager'),
      $container->get('user.data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ngf_user_private_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'item',
      '#markup' => t('<h2>Private settings</h2>'),
    ];
    $form['info'] = [
      '#type' => 'item',
      '#markup' => t('<p>Private settings information</p>'),
    ];
    $form['setting_wrapper'] = [
      '#type' => 'container',
    ];
    $settings = $this->userSettingsManager->getList();
    foreach ($settings as $key => $setting) {
      $form['setting_wrapper'][$key] = [
        '#prefix' =>  '<div class="form__block--toggle">',
        '#suffix' =>  '</div>',
        '#type' => 'checkbox',
        '#title' => t('<span class="onoffswitch-inner"></span> <span class="label-text">@title</span>',[
          '@title' => $setting['title'],

        ]),
        '#default_value' => $this->userSettingsManager->getSetting($key),
        '#attributes' => [
          'id' => 'edit-' . str_replace('_', '-', $key),
        ],
      ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#attributes' => [
        'class' => [
          'btn btn--green btn-large',
        ]
      ],
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    foreach (array_keys($this->userSettingsManager->getList()) as $setting) {
      if (isset($values[$setting])) {
        $this->userSettingsManager->setSetting($setting, $values[$setting]);
      }
    }

    \Drupal::getContainer()->get('messenger')->addMessage(t('Your private settings have been updated'));
  }

}
