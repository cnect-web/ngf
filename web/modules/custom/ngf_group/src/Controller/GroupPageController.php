<?php

namespace Drupal\ngf_group\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\views\Views;

/**
 * Discover page controller.
 */
class GroupPageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function discoverPage() {

    $render_array = [];
    $user = \Drupal::currentUser();

    // Check if the current user can create groups.
    if ($user->hasPermission('create ngf discussion group group')) {
      $url = Url::fromUri('internal:/group/add/ngf_discussion_group', []);
      $link = Link::fromTextAndUrl(t('Create a Group'), $url);

      // Add the create group link to the render array.
      $render_array[] = [
        '#type' => 'markup',
        '#markup' => $link->toString()->getGeneratedLink()
      ];
    }

    $view = Views::getView('ngf_groups');
    $view->setDisplay('groups_block');

    // Add the groups view title to the render array.
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . $view->getTitle() .'</h1>',
    ];

    // Add the groups view to the render array.
    $render_array[] = $view->render();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function publicationsPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function eventsPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function libraryPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function sharedContentPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function membersPage(EntityInterface $group) {

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the page title to the render array.
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . t("Members") .'</h1>',
    ];

    // Add the groups view to the render array.
    $render_array['ngf_group_members']['view'] = [
      '#type' => 'view',
      '#name' => 'ngf_group_members',
      '#display_id' => 'block_1',
      '#arguments' => [
        $group->id(),
      ],
    ];

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function followersPage(EntityInterface $group) {

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the page title to the render array.
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . t("Followers") .'</h1>',
    ];

    // Add the groups view to the render array.
    $render_array['ngf_group_followers']['view'] = [
      '#type' => 'view',
      '#name' => 'ngf_group_followers',
      '#display_id' => 'block_1',
      '#arguments' => [
        $group->id(),
      ],
    ];

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function groupHeader(EntityInterface $group) {
    $view_builder = \Drupal::entityManager()->getViewBuilder('group');
    return $view_builder->view($group, 'header');
  }

}
