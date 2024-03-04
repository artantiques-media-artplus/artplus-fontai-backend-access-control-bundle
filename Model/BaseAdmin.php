<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use App\Model;
use Fontai\Bundle\SecurityBundle\Model\UserTrait;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Security\Core\User\UserInterface;


abstract class BaseAdmin implements UserInterface
{
  use UserTrait;

  protected $allowed_permission_modules;
  protected $all_permission_module_actions;
  protected $credentials;
  protected $editable_permission_groups;

  public function __construct()
  {
  }

  public function __toString()
  {
    return $this->getFullName();
  }

  public function getFullName()
  {
    return sprintf('%s %s', $this->getLastName(), $this->getFirstName());
  }

  public function getInfopanel()
  {
    foreach ($this->getAdminPermissionGroupsJoinPermissionGroup() as $adminPermissionGroup)
    {
      if ($adminPermissionGroup->getPermissionGroup()->getInfopanel())
      {
        return TRUE;
      }
    }
    
    return FALSE;
  }

  protected function getGroupPermissionModuleActions()
  {
    return Model\PermissionModuleActionQuery::create()
    ->joinWithPermissionModule()
    ->joinWithPermissionAction()
    ->joinPermissionGroupPermissionModuleAction()
    ->usePermissionGroupPermissionModuleActionQuery()
      ->joinPermissionGroup()
      ->usePermissionGroupQuery()
        ->joinAdminPermissionGroup()
        ->useAdminPermissionGroupQuery()
          ->filterByAdmin($this)
        ->endUse()
      ->endUse()
    ->endUse()
    ->find();
  }

  public function getAllPermissionModuleActions(
    Criteria $criteria = NULL,
    ConnectionInterface $con = NULL
  )
  {
    if ($this->all_permission_module_actions === NULL)
    {
      $this->all_permission_module_actions = $this->getGroupPermissionModuleActions();

      foreach ($this->getPermissionModuleActions() as $permissionModuleAction)
      {
        $this->all_permission_module_actions->append($permissionModuleAction);
      }
    }

    return $this->all_permission_module_actions;
  }

  public function getEditablePermissionGroups(
    Criteria $criteria = NULL,
    ConnectionInterface $con = NULL
  )
  {
    if ($this->editable_permission_groups === NULL)
    {
      $query = Model\PermissionGroupQuery::create(NULL, $criteria);
      $query->where(
        'NOT EXISTS(
          SELECT *
          FROM `' . Model\Map\PermissionGroupPermissionModuleActionTableMap::TABLE_NAME . '` AS pgr
          WHERE pgr.PERMISSION_GROUP_ID = PermissionGroup.Id
          AND pgr.PERMISSION_MODULE_ACTION_ID NOT IN(' . implode(',', $this->getAllPermissionModuleActions()->getPrimaryKeys()) . ')
        )'
      );

      $this->editable_permission_groups = $query->find();
    }

    return $this->editable_permission_groups;
  }

  public function getRoles()
  {
    return ['ROLE_ADMIN'];
  }

  public function hasCredential($credentials)
  {
    $this->initCredentials();

    if (!is_array($credentials))
    {
      $credentials = [$credentials];
    }

    foreach ($credentials as $credential)
    {
      if (in_array($credential, $this->credentials))
      {
        return TRUE;
      }
    }

    return FALSE;
  }

  public function getAllowedPermissionModules()
  {
    $this->initCredentials();

    return $this->allowed_permission_modules;
  }

  protected function initCredentials()
  {
    if ($this->credentials !== NULL)
    {
      return;
    }

    $this->credentials = [];
    $this->allowed_permission_modules = [];

    foreach ($this->getAllPermissionModuleActions() as $permissionModuleAction)
    {
      $permissionModule = $permissionModuleAction->getPermissionModule();
      $permissionAction = $permissionModuleAction->getPermissionAction();

      $this->credentials[] = sprintf(
        '%s-%s',
        $permissionModule->getName(),
        $permissionAction->getName()
      );
      
      $this->allowed_permission_modules[$permissionModule->getName()] = $permissionModule;
    }
  }
}
