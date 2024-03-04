<?php
namespace Fontai\Bundle\BackendAccessControlBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class BackendAccessControlExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container)
  {
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('backend_access_control.yaml');
  }

  public function getAlias()
  {
    return 'backend_access_control';
  }
}