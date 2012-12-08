<?php

namespace MockServer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MockServer\Manager\ProcessManager;
use MockServer\Exception\ServerPortInUseException;
use MockServer\Exception\SocketConnectionException;

class StartServerCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var String
     */
    protected $pidFile;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('mock:server:start')
            ->setDescription('Start a new Mock Server')
            ->addArgument('class', InputArgument::REQUIRED, 'What server type should be started?')
            ->addArgument('host', InputArgument::REQUIRED, 'What hostname should the server use?')
            ->addArgument('port', InputArgument::REQUIRED, 'What port should the server run on?')
            ->addArgument('pidFile', InputArgument::REQUIRED, 'Where do you want to store your pid files?')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Run in dev mode to autoload the ExampleBundle')
        ;
    }

    /**
     * Execute
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // When in dev mode we'll need tyo autoload the ExampleBundle
        if (true === $input->getOption('dev')) {
            spl_autoload_register(function($class) {
                $classFile = __DIR__ . '/../../../examples/' . str_replace('\\', '/', $class) . '.php';
                if (true === file_exists($classFile)) {
                    include_once($classFile);
                }
            });
        }

        $class = $input->getArgument('class');
        $host = $input->getArgument('host');
        $port = $input->getArgument('port');

        $this->processManager = new ProcessManager($input->getArgument('pidFile'), new \Monolog\Logger('\MockServer\Manager\ProcessManager'));
        $this->processManager->flush();

        $server = new $class($port, $host, new \Monolog\Logger($class));

        try {
            $this->processManager->add(getmypid(), $host, $port);
            $this->processManager->save();

            $server->start();
        } catch (SocketConnectionException $e) {
            // Server could not start as port is already in use
            $this->processManager->flush(false, array('host' => $host, 'port' => $port));
            throw new ServerPortInUseException("Could not bind to {$host}:{$port} as it was already in use. Killed it though :D");
        }
    }
}
