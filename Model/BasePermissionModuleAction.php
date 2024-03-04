<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;


abstract class BasePermissionModuleAction
{
  public function __construct()
  {
  }
  
  public function __toString()
  {
    $permissionModule = $this->getPermissionModule();
    $parentPermissionModule = $permissionModule->getPermissionModuleRelatedByPermissionModuleId();

    return sprintf(
      '%s (%s) - %s',
      ($parentPermissionModule ? $parentPermissionModule->getTitle() . ' / ' : NULL) . $permissionModule->getTitle(),
      $permissionModule->getName(),
      $this->getPermissionAction()->getTitle()
    );
  }

  public function getProjectsString()
  {
    return $this->getPermissionModule()->getProjectsString();
  }
}
