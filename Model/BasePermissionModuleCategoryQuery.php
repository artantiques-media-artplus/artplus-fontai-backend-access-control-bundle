<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BasePermissionModuleCategoryQuery extends ModelCriteria
{
  protected function preSelect(ConnectionInterface $con)
  {
    $this->orderByPriority(Criteria::DESC);

    if ($this->hasJoin('PermissionModuleCategoryI18n'))
    {
      $this
      ->useQuery('PermissionModuleCategoryI18n')
        ->orderByName()
      ->endUse();
    }
  }
}
