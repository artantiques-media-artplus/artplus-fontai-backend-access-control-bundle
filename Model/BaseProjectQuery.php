<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BaseProjectQuery extends ModelCriteria
{
  protected function preSelect(ConnectionInterface $con)
  {
    $this
    ->orderByPriority(Criteria::DESC)
    ->orderByName();
  }
}
