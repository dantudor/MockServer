<?php

namespace MockServer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Server Start Command
 */
class InstallCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('mock:server:install')
            ->setDescription('Install an example mock');
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  InputInterface
     * @param OutputInterface $output OutputInterface
     *
     * @return int|null|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        $rootDir = $this->getApplication()->getKernel()->getRootDir();

        $filesystem->copy(__DIR__ . '/../../../BasicUsage/MockKernel.php', $rootDir . '/MockKernel.php');
        $filesystem->mkdir($rootDir . '/mock/acme');
        $filesystem->copy(__DIR__ . '/../../../BasicUsage/mock/acme/config.yml', $rootDir . '/mock/acme/config.yml');
        $filesystem->copy(__DIR__ . '/../../../BasicUsage/mock/acme/parameters.yml', $rootDir . '/mock/acme/parameters.yml');
        $filesystem->copy(__DIR__ . '/../../../BasicUsage/mock/acme/routing.yml', $rootDir . '/mock/acme/routing.yml');
        $filesystem->copy(__DIR__ . '/../../../BasicUsage/mock/acme/security.yml', $rootDir . '/mock/acme/security.yml');
    }
}
