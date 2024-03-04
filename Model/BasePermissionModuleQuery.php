<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BasePermissionModuleQuery extends ModelCriteria
{
  public function findParentsOrdered(ConnectionInterface $con = null)
  {
    return $this
    ->filterByPermissionModuleId(NULL)
    ->joinWithI18n()
    ->useI18nQuery()
      ->orderByTitle()
    ->endUse()
    ->find();
  }
}
