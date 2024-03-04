<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Form;

use Fontai\Bundle\SecurityBundle\Form\UserType as SecurityUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as SymfonyType;
use Symfony\Component\Validator\Constraints;


class AccountType extends SecurityUserType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('first_name', SymfonyType\TextType::class, [
      'label'       => 'Jméno',
      'required'    => TRUE,
      'constraints' => [
        new Constraints\NotBlank()
      ],    
    ]);

    $builder->add('last_name', SymfonyType\TextType::class, [
      'label'       => 'Příjmení',
      'required'    => TRUE,
      'constraints' => [
        new Constraints\NotBlank()
      ],    
    ]);

    $builder->add('smart_menu', SymfonyType\CheckboxType::class, [
      'label'       => 'Chytré menu',
      'required'    => FALSE
    ]);

    $builder->add('smart_header', SymfonyType\CheckboxType::class, [
      'label'       => 'Chytré filtry',
      'required'    => FALSE
    ]);

    parent::buildForm($builder, $options);
  }
}
