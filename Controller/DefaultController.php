<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Controller;

use App\Model;
use Fontai\Bundle\BackendAccessControlBundle\Form\AccountType;
use Fontai\Bundle\GeneratorBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class DefaultController extends AbstractController
{
  use ControllerTrait;

  protected $translator;

  public function __construct(
    TranslatorInterface $translator
  )
  {
    $this->translator = $translator;
  }

  public function account(
    Request $request,
    UserInterface $user,
    UserPasswordHasherInterface $passwordHasher
  )
  {
    $form = $this->createForm(
      AccountType::class,
      $user,
      [
        'section' => 'backend',
        'entity_user' => Model\Admin::class
      ]
    );
    
    $form->handleRequest($request);

    if ($form->isSubmitted())
    {
      if (!$form->isValid())
      {
        return $this->parseErrors('Formulář obsahuje tyto chyby:', $form);
      }
      else
      {
        if ($password = $form['password']->getData())
        {
          $user
          ->setPassword($passwordHasher->hashPassword($user, $password))
          ->setInit(TRUE);
        }

        $user->save();

        return $this->json([
          'success'  => TRUE,
          'redirect' => $request->getUri()
        ]);
      }
    }

    return $this->render('@BackendAccessControl/default/account.html.twig', [
      'edit_form' => $form->createView()
    ]);
  }

  public function menuSort(
    Request $request,
    UserInterface $user
  )
  {
    $data = $request->request->get('data', []);
    
    Model\PermissionModulePriorityQuery::create()
    ->filterByAdmin($user)
    ->filterByPermissionModuleId($data)
    ->delete();

    foreach ($data as $priority => $moduleId)
    {
      $permissionModulePriority = new Model\PermissionModulePriority();
      $permissionModulePriority
      ->setAdmin($user)
      ->setPermissionModuleId($moduleId)
      ->setPriority($priority)
      ->save();
    }
    
    return new Response('');
  }

  public function changeProject(
    Request $request,
    SessionInterface $session
  )
  {
    $projectId = $request->get('projectId');

    if ($projectId)
    {
      $admin = $this->getUser();

      $project = Model\ProjectQuery::create()
      ->joinAdminProject()
      ->useAdminProjectQuery()
        ->filterByAdmin($admin)
      ->endUse()
      ->findOneById($projectId);

      if ($project)
      {
        $admin
        ->setLastProject($project)
        ->save();

        $session->set('projectId', $project->getId());
        $session->set('projectName', $project->getName());
      }
    }

    return $this->redirectToRoute('app_backend_index');
  }
}