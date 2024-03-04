<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BasePermissionGroupQuery extends ModelCriteria
{
  protected function preSelect(ConnectionInterface $con)
  {
    $this->orderByName();
  }
}
