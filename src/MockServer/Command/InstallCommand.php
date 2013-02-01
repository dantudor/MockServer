<?php

namespace MockServer\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MockServer\Generator\MockGenerator;

/**
 * Server Start Command
 */
class InstallCommand extends ContainerAwareCommand
{
    /**  @var OutputInterface */
    protected $output;

    /** @var \MockServer\Generator\MockGenerator */
    protected $generator;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('mock:server:install')
            ->setDescription('Install a new mock server')
            ->addArgument('name', InputArgument::REQUIRED, 'A unique name for your new mock server instance <info>(lowercase, no spaces)</info>')
            ->setHelp('The <info>%command.name%</info> command will create a new mock server instance.');
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
        /** @var $filesystem \Symfony\Component\Filesystem\Filesystem */
        $filesystem = $this->getContainer()->get('filesystem');

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $name = $url = preg_replace('/[^\da-z]/i', '', strtolower($input->getArgument('name')));

        if (false === $filesystem->exists($rootDir . '/MockKernel.php')) {
            $filesystem->copy(__DIR__ . '/../Resources/Deploy/MockKernel.php', $rootDir . '/MockKernel.php');
            $output->writeln(sprintf('Installing new MockKernel into <info>%s</info>', $rootDir . '/MockKernel.php'));
        }

        $output->writeln(sprintf('Creating basic configuration for <info>%s</info>', $name));

        $generator = $this->getGenerator();
        $generator->generate($rootDir, $name);

        $output->writeln('Generating the new mock server: <info>OK</info>');
    }


    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new MockGenerator($this->getContainer()->get('filesystem'), __DIR__.'/../Resources/Deploy');
        }

        return $this->generator;
    }
}
