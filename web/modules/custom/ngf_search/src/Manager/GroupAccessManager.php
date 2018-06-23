<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 6/22/18
 * Time: 10:35 PM
 */

namespace Drupal\ngf_search\Manager;


use Drupal\group\Entity\GroupInterface;
use Drupal\group\Entity\GroupContent;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountInterface;

class GroupAccessManager
{
    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

    /**
     * The current user.
     *
     * @var \Drupal\user\Entity\User
     */
    protected $currentUserAccount = NULL;

    protected $isAdmin = NULL;

    public function __construct(AccountInterface $current_user) {
        $this->currentUser = $current_user;
    }

    protected function getCurrentUserAccount() {
        if (empty($this->currentUserAccount)) {
            $this->currentUserAccount = User::load($this->currentUser->id());
        }
        return $this->currentUserAccount;
    }

    public function checkNode(NodeInterface $node) {

    }

    public function checkGroup(GroupInterface $group)
    {
        if ($this->isAdmin()) {
          return TRUE;
        }

        $group_to_check = $group;
        if ($group->get('field_ngf_group_visibility')->value != NGF_GROUP_SECRET && $group_content = GroupContent::loadByEntity($group)) {
          kint($group_content);
            $group_to_check = $group_content->getGroup();
        }
        return $this->checkGroupAccess($group_to_check);
    }

    private function checkGroupAccess($group) {
        $result = TRUE;
        // Members can see the group
        if ($group->getMember($this->getCurrentUserAccount())) {
            $result = TRUE;
        } elseif ($group->get('field_ngf_group_visibility')->value == NGF_GROUP_SECRET) {
            $result = FALSE;
        }

        return $result;
    }

    private function isAdmin() {
      if (is_null($this->isAdmin)) {
        $this->isAdmin = in_array('administrator', $this->currentUser->getRoles());
      }
      return $this->isAdmin;
    }
}