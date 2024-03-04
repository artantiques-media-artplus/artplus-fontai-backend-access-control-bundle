<?php
namespace Fontai\Bundle\BackendAccessControlBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;


class GenerateConfigCommand extends Command
{
  protected $projectDir;

  public function __construct(string $projectDir)
  {
    $this->projectDir = $projectDir;

    parent::__construct();
  }

  protected function configure()
  {
    $this
    ->setDescription('Generates Fontai Security Bundle configuration.')
    ->setHelp('This command generates Fontai Security Bundle section configuration.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $path = sprintf('%s/config/packages/fontai_security.yaml', $this->projectDir);
    $config = Yaml::parseFile($path);

    $config['fontai_security']['sections']['backend'] = [
      'entity_user' => 'App\Model\Admin',
      'entity_login_attempt' => 'App\Model\AdminLoginAttempt',
      'role' => 'ROLE_ADMIN',
      'route_prefix' => '/backend',
      'login_target' => 'app_backend_index',
      'requires_channel' => 'https',
      'init_target' => 'backend_access_control_default_account',
      'init_email_template' => 'Nový účet administrátora',
      'recovery_email_template' => 'Zapomenuté heslo administrátora'
    ];

    file_put_contents($path, Yaml::dump($config, 5, 4));

    return 0;
  }
}