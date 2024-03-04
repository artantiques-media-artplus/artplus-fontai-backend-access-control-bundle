<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;


abstract class BaseAdminQuery extends ModelCriteria
{  
  protected function preSelect(ConnectionInterface $con)
  {
    $this
    ->orderByLastName()
    ->orderByFirstName();
  }

  public function findOneByInitHash(
    string $init_hash,
    ConnectionInterface $con = NULL
  )
  {
    return $this
    ->filterByIsActive(TRUE)
    ->filterByInit(FALSE)
    ->filterByInitHash($init_hash)
    ->findOne($con);
  }

  public function findOneByEmail(
    string $email,
    ConnectionInterface $con = NULL
  )
  {
    return $this
    ->filterByIsActive(TRUE)
    ->filterByInit(TRUE)
    ->filterByEmail($email)
    ->findOne($con);
  }
}
