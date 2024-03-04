<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Model;


abstract class BasePermissionAction
{
  public function __construct()
  {
  }
  
  public function __toString()
  {
    return (string) $this->getTitle();
  }
}
