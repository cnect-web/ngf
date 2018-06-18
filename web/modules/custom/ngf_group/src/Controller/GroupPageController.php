<?php

namespace Drupal\ngf_group\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ngf_group\Entity\Decorator\NGFGroup;
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
  public function groupInfo(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group.
    $render_array['header'] = $this->groupView($group, 'full');
    $render_array['group_tabs'] = $gD->getGroupTabs();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function publicationsPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupView($group);

    $render_array['group_tabs'] = $gD->getGroupTabs();

    $view = Views::getView('ngf_group_publications');
    $view->setArguments([$group->id()]);
    $view->setDisplay('discussions');
    $view->preExecute();
    $view->execute();

    // Add the groups view title to the render array.
    $title = $view->getTitle();
    if ($title) {
      $render_array['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $title,
      ];
    }

    // Add the groups view to the render array.
    $render_array['content'] = $view->render();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function eventsPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupView($group);

    $render_array['group_tabs'] = $gD->getGroupTabs();

    $view = Views::getView('ngf_group_events');
    $view->setArguments([$group->id()]);
    $view->setDisplay('events');
    $view->preExecute();
    $view->execute();

    // Add the groups view title to the render array.
    $title = $view->getTitle();
    if ($title) {
      $render_array['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $title,
      ];
    }

    // Add the groups view to the render array.
    $render_array['content'] = $view->render();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function libraryPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupView($group);

    $render_array['group_tabs'] = $gD->getGroupTabs();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function sharedContentPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupView($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function membersPage(EntityInterface $group) {

    // Add the group header.
    $render_array['header'] = $this->groupView($group);

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
    $render_array['header'] = $this->groupView($group);

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
  public function groupView(EntityInterface $group, $view_mode = 'header') {
    $view_builder = \Drupal::entityManager()->getViewBuilder('group');
    return $view_builder->view($group, $view_mode);
  }

}
