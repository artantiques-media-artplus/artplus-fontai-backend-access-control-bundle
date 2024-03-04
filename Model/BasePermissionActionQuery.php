<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BasePermissionActionQuery extends ModelCriteria
{
  protected function preSelect(ConnectionInterface $con)
  {
    $this->orderByName();
  }

  public function filterByPermissionModule(
    $permissionModule,
    $comparison = Criteria::EQUAL
  )
  {
    return $this
    ->usePermissionModuleActionQuery()
      ->filterByPermissionModule($permissionModule, $comparison)
    ->endUse();
  }
}
