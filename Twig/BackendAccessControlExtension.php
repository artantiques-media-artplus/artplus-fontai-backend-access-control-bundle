<?php

namespace Fontai\Bundle\BackendAccessControlBundle\Twig;

use App\Model;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackendAccessControlExtension extends AbstractExtension
{
  protected $formFactory;
  protected $requestStack;
  protected $router;
  protected $session;
  protected $tokenStorage;

  public function __construct(
    FormFactoryInterface $formFactory,
    RequestStack $requestStack,
    RouterInterface $router,
    SessionInterface $session,
    TokenStorageInterface $tokenStorage
  )
  {
    $this->formFactory = $formFactory;
    $this->requestStack = $requestStack;
    $this->router = $router;
    $this->session = $session;
    $this->tokenStorage = $tokenStorage;
  }

  public function getFunctions()
  {
    return [
      new TwigFunction(
        'backend_admin_box',
        [$this, 'getAdminBox'],
        [
          'needs_environment' => TRUE,
          'is_safe' => ['html']
        ]
      ),
      new TwigFunction(
        'backend_menu',
        [$this, 'getMenu'],
        [
          'needs_environment' => TRUE,
          'is_safe' => ['html']
        ]
      ),
      new TwigFunction(
        'backend_title',
        [$this, 'getTitle'],
        [
          'needs_environment' => TRUE,
          'is_safe' => ['html']
        ]
      )
    ];
  }

  public function getAdminBox(\Twig_Environment $environment)
  {
    return $environment->render(
      '@BackendAccessControl/extension/_admin_box.html.twig',
      [
        'form' => $this->getChangeProjectForm()->createView(),
        'logged' => $this->getLoggedAdmins()
      ]
    );
  }

  public function getMenu(\Twig_Environment $environment)
  {
    return $environment->render('@BackendAccessControl/extension/_menu.html.twig', $this->getMenuData());
  }

  public function getTitle(\Twig_Environment $environment)
  {
    return $environment->render('@BackendAccessControl/extension/_title.html.twig', $this->getMenuData());
  }

  protected function getChangeProjectForm()
  {
    $user = $this->tokenStorage->getToken()->getUser();

    return $this->formFactory
    ->createNamedBuilder('backend_project')
    ->setAction($this->router->generate('backend_access_control_default_changeProject'))
    ->setMethod('POST')
    ->add('projectId', ChoiceType::class, [
      'label' => FALSE,
      'choices' => $user->getProjects()->toKeyValue('Name', 'Id'),
      'data' => $this->session->get('projectId'),
      'attr' => [
        'class' => 'submitOnChange'
      ]
    ])
    ->getForm();
  }

  protected function getLoggedAdmins()
  {
    return Model\AdminQuery::create()
    ->withColumn('CONCAT(Admin.LastName, \' \', Admin.FirstName)', 'FullName')
    ->select('FullName')
    ->filterByLastActivityAt('-30 minute', Criteria::GREATER_EQUAL)
    ->find();
  }

  protected function getMenuData()
  {
    $request = $this->requestStack->getCurrentRequest();
    $user = $this->tokenStorage->getToken()->getUser();
    $locale = $request->getLocale();

    $modules = Model\PermissionModuleQuery::create()
    ->withColumn(
      'IFNULL(
        (
          SELECT `pmp`.`priority`
          FROM `' . Model\Map\PermissionModulePriorityTableMap::TABLE_NAME . '` AS `pmp`
          WHERE `pmp`.`permission_module_id` = PermissionModule.Id
          AND `pmp`.`admin_id` = ' . intval($user->getId()) . '
        ),
        PermissionModule.Priority
      )',
      'TmpPrio'
    )
    ->joinWithI18n($locale)
    ->joinPermissionModuleProject()
    ->joinWithPermissionModuleCategory()
    ->filterByName(array_keys($user->getAllowedPermissionModules()))
    ->usePermissionModuleProjectQuery()
    ->filterByProjectId($this->session->get('projectId'))
    ->endUse()
    ->usePermissionModuleCategoryQuery()
    ->joinWithI18n($locale)
    ->orderByPriority(Criteria::DESC)
    ->endUse()
    ->orderByTmpPrio(Criteria::DESC)
    ->orderByName()
    ->find();

    $moduleIds = $modules->getColumnValues('Id');
    $moduleTree = [];
    $categoryTree = [];
    $activeModule = $request->attributes->get('currentModule');
    $activeCategoryId = NULL;
    $isHomepage = $request->attributes->get('_route') == 'app_backend_index';

    $defaultCategory = Model\PermissionModuleCategoryQuery::create()
    ->joinWithI18n($locale)
    ->filterByIsDefault(TRUE)
    ->findOne();

    if ($defaultCategory)
    {
      $categoryTree[$defaultCategory->getId()] = [$defaultCategory, []];
    }

    foreach ($modules as $module)
    {
      $moduleId = intval($module->getPermissionModuleId());
      $categoryId = $module->getPermissionModuleCategoryId();

      if ($activeModule == $module)
      {
        $activeCategoryId = $categoryId;
      }

      if ($moduleId && in_array($moduleId, $moduleIds))
      {
        if (!isset($moduleTree[$moduleId]))
        {
          $moduleTree[$moduleId] = [];
        }

        $moduleTree[$moduleId][] = $module;
      }
      else
      {
        $category = $module->getPermissionModuleCategory();

        if (!isset($categoryTree[$categoryId]))
        {
          $categoryTree[$categoryId] = [
            $category,
            []
          ];
        }

        $categoryTree[$categoryId][1][] = $module;

        if ($isHomepage && $category->getIsDefault())
        {
          $activeCategoryId = $categoryId;
        }
      }
    }

    return [
      'categoryTree' => $categoryTree,
      'moduleTree' => $moduleTree,
      'activeCategoryId' => $activeCategoryId
    ];
  }

  public function getName()
  {
    return 'backend_access_control_extension';
  }
}