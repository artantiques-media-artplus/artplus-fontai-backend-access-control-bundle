<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;


abstract class BaseAdminPermissionGroup
{
  public function __construct()
  {
  }
  
  public function __toString()
  {
    return $this->getPermissionGroupId() ? $this->getPermissionGroup()->getName() : NULL;
  }
}