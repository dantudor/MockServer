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
use MockServer\Server\SymfonyServer;

/**
 * Server Start Command
 */
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
     * @var \MockServer\Manager\ProcessManager
     */
    protected $processManager;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('mock:server:start')
            ->setDescription('Start a new Mock Server')
            ->addArgument('kernel_root_dir', InputArgument::REQUIRED, 'Where is your AppKernel?')
            ->addArgument('environment', InputArgument::REQUIRED, 'What environment should be used?')
            ->addArgument('host', InputArgument::REQUIRED, 'What hostname should the server use?')
            ->addArgument('port', InputArgument::REQUIRED, 'What port should the server run on?')
            ->addArgument('pidFile', InputArgument::REQUIRED, 'Where do you want to store your pid files?');
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
        $this->processManager = new ProcessManager($input->getArgument('pidFile'));
        $this->processManager->flush(false);

        $host = $input->getArgument('host');
        $port = $input->getArgument('port');

        /** @var $server SymfonyServer */
        $server = new SymfonyServer($input->getArgument('kernel_root_dir'), $input->getArgument('environment'), $port, $host);

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
