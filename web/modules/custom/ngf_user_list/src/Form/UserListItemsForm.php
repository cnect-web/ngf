<?php

namespace Drupal\ngf_user_list\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\flag\FlagService;
use Drupal\ngf_user_profile\FlagTrait;
use Drupal\ngf_user_list\Entity\UserList;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that adds user list item.
 */
class UserListItemsForm extends FormBase {

  use FlagTrait;

  /**
   * The flag service.
   *
   * @var \Drupal\flag\FlagService
   */
  protected $flag;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * User list manager.
   *
   * @var \Drupal\ngf_user_list\Manager\UserListManager
   */
  protected $userListManager;

  /**
   * Constructs a Drupal\ngf_user_list\Form\UserListItemForm object.
   *
   * @param \Drupal\flag\FlagService $flag
   *   The flag service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(
    FlagService $flag,
    AccountInterface $current_user,
    UserListManager $userListManager
  ) {
    $this->flag = $flag;
    $this->currentUser = $current_user;
    $this->userListManager = $userListManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flag'),
      $container->get('current_user'),
      $container->get('ngf_user_list.user_list')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ngf_user_list_items';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $ngf_user_list = NULL) {

    $form['actions'] = [
      '#title' => $this->t('Action'),
      '#type' => 'select',
      '#options' => [
        '' => $this->t('Select action'),
        'follow' => $this->t('Follow'),
        'unfollow' => $this->t('Unfollow'),
        'remove' => $this->t('Remove from the list'),
      ],
    ];

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    );

    $list_items = $this->userListManager->getUserListItems($ngf_user_list);
    $render = [];
    $items = [];
    foreach ($list_items as $list_item) {
      $items[] = $this->entityTypeManager()
        ->getViewBuilder('user')
        ->view($list_item, 'compact');
      $items[] = [
        '#theme' => 'item_list',
        '#items' => [
          Link::fromTextAndUrl(t('Remove user'), Url::fromRoute('ngf_user_list.remove_user_list_item', ['username' => $list_item->getAccountName(), 'list_id' => $ngf_user_list->id()])),
        ],
        '#attributes' => [
          'class' => [
            'links inline',
          ]
        ]
      ];
    }
    $render[] = [
      '#type' => 'link',
      '#title' => t('Add user'),
      '#url' => Url::fromRoute('ngf_user_list.add_list_item', ['ngf_user_list' => $ngf_user_list->id()]),
      '#attributes' => [
        'class' => [
          'btn btn--blue',
        ]
      ]
    ];
    $render[] = $items;

    $form['list_id'] = array(
      '#type' => 'hidden',
      '#value' => $ngf_user_list->id(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $list = UserList::load($form_state->getValue('list_id'));
    $user = User::load($form_state->getValue('user_id'));

    if (empty($user)) {
      $form_state->setErrorByName('user_id', $this->t('User is not found.'));
    } elseif (empty($list)) {
      $form_state->setErrorByName('list_id', $this->t('List is not found.'));
    } elseif ($list->getOwnerId() !== $this->currentUser->id()) {
      $form_state->setErrorByName('list_id', $this->t('This list does not belong to you.'));
    } else {
      $flag = $this->getListItemFlag();
      if ($flag->isFlagged($list, $user)) {
        $form_state->setErrorByName('list_id', $this->t('You already have user @username in the list', ['@username' => $user->getDisplayName()]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $list = UserList::load($form_state->getValue('list_id'));
    $user = User::load($form_state->getValue('user_id'));

    $flag = $this->getListItemFlag();
    $this->flag->flag($flag, $list, $user);
    drupal_set_message($this->t('User @username has been added to the list', ['@username' => $user->getDisplayName()]));
    $form_state->setRedirect('ngf_user_list.list_items', ['ngf_user_list' => $list->id()]);
  }

}
