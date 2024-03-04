<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use App\Model;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Container;


abstract class BasePermissionModule
{
  public function __construct()
  {
  }
  
  public function __toString()
  {
    return (string) $this->getTitle();
  }

  public function getProjectsString()
  {
    $r = [];

    foreach ($this->getPermissionModuleProjects() as $permissionModuleProject)
    {
      $r[] = $permissionModuleProject->getProjectId();
    }
    
    return implode(',', $r);
  }

  public function getRouteNamePrefix()
  {
    return sprintf('fontai_generator_%s', Container::underscore(explode('\\', $this->getName())[1]));
  }

  public function getPermissionActions(
    Criteria $criteria = NULL,
    ConnectionInterface $con = NULL
  )
  {
    return Model\PermissionActionQuery::create(NULL, $criteria)
    ->filterByPermissionModule($this)
    ->find($con);
  }

  public function setPermissionActions(
    Collection $permissionActions,
    ConnectionInterface $con = NULL
  )
  {
    $currentPermissionActions = $this->getPermissionActions();

    $permissionActionsScheduledForDeletion = $currentPermissionActions->diff($permissionActions);

    foreach ($permissionActionsScheduledForDeletion as $toDelete)
    {
      Model\PermissionModuleActionQuery::create()
      ->filterByPermissionAction($toDelete)
      ->filterByPermissionModule($this)
      ->delete();
    }

    foreach ($permissionActions as $permissionAction)
    {
      if (!$currentPermissionActions->contains($permissionAction))
      {
        $permissionModuleAction = new Model\PermissionModuleAction();
        $permissionModuleAction
        ->setPermissionAction($permissionAction)
        ->setPermissionModule($this);
      }
    }

    return $this;
  }
}
