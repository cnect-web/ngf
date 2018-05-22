<?php

namespace Drupal\ngf_group\Controller;

use Drupal\Core\Controller\ControllerBase;
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

    $view = Views::getView('groups');
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
  public function publicationsPage() {
    // Add the group header.
    $render_array['header'] = $this->groupHeader();
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function eventsPage() {
    // Add the group header.
    $render_array['header'] = $this->groupHeader();
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function libraryPage() {
    // Add the group header.
    $render_array['header'] = $this->groupHeader();
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function sharedContentPage() {
    // Add the group header.
    $render_array['header'] = $this->groupHeader();
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function membersPage() {
    // Add the group header.
    $render_array['header'] = $this->groupHeader();

    // Add the page title to the render array.
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . t("Members") .'</h1>',
    ];
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function followersPage() {
    $render_array = [];
    $render_array['header'] = $this->groupHeader();

    // Add the page title to the render array.
    $render_array[] = [
      '#type' => 'markup',
      '#markup' => '<h1>' . t("Followers") .'</h1>',
    ];

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function groupHeader() {
    $group_header = NULL;
    $route_match = \Drupal::routeMatch();
    if (($route = $route_match->getRouteObject()) && ($parameters = $route->getOption('parameters'))) {
      foreach ($parameters as $name => $options) {
        if (isset($options['type']) && strpos($options['type'], 'entity:') === 0) {
          $entity = $route_match->getParameter($name);
          $view_builder = \Drupal::entityManager()->getViewBuilder('group');
          $group_header = $view_builder->view($entity, 'header');
        }
      }
    }
    return $group_header;
  }

}
