<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;


abstract class BasePermissionModuleCategory
{
  public function __construct()
  {
  }
  
  public function __toString()
  {
    return (string) $this->getName();
  }
}
