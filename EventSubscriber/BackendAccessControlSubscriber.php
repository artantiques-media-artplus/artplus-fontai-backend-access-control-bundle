<?php
namespace Fontai\Bundle\BackendAccessControlBundle\EventSubscriber;

use Fontai\Bundle\FccBundle\Service\Fcc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;


class BackendAccessControlSubscriber implements EventSubscriberInterface
{
  protected $requestStack;
  protected $fcc;

  public function __construct(
    RequestStack $requestStack,
    Fcc $fcc
  )
  {
    $this->requestStack = $requestStack;
    $this->fcc = $fcc;
  }

  public function onAuthenticationSuccess(AuthenticationEvent $event)
  {
    $token = $event->getAuthenticationToken();

    if (!$this->isBackendToken($token))
    {
      return;
    }

    $admin = $token->getUser();

    if (!is_object($admin))
    {
      return;
    }

    if ($admin->getInit())
    {
      $request = $this->requestStack->getCurrentRequest();
      $currentModuleName = substr(current(explode('::', $request->attributes->get('_controller'))), 15, -10);

      $projects = $admin->getProjects();
      $project = $admin->getLastProject();

      if (!$project)
      {
        $project = $projects[0];
      }

      $session = $request->getSession();

      $session->set('projectId', $project->getId());
      $session->set('projectName', $project->getName());

      $modules = $admin->getAllowedPermissionModules();

      if (isset($modules[$currentModuleName]))
      {
        $request->attributes->set('currentModule', $modules[$currentModuleName]);
      }

      $request->attributes->set('allowedModules', array_keys($modules));

      $this->fcc
      ->setProjectId($project->getFccId())
      ->setAppKey($project->getFccKey())
      ->setAppPass($project->getFccPass())
      ->setUserName($admin->getFullName());
    }
  }
  
  protected function isBackendToken(TokenInterface $token)
  {
    return method_exists($token, 'getProviderKey') && $token->getProviderKey() == 'backend';
  }

  public static function getSubscribedEvents()
  {
    return [
      AuthenticationEvents::AUTHENTICATION_SUCCESS => [
        ['onAuthenticationSuccess', 0]
      ]
    ];
  }
}