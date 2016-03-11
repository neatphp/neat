<?php
namespace Neat\Command;

/**
 * Daemon command
 */
abstract class AbstractDaemonCommand
{
    /**
     * Path to PID file
     *
     * @var string
     */
    protected $pidfile = '/tmp/mgw_daemon.pid';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('daemon')
            ->setDescription('Daemon process')
            ->addArgument('cmd', InputArgument::REQUIRED, "The command [start,stop]");
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $command = $input->getArgument('cmd');
            switch ($command) {
                case 'start':
                    $this->start($output);

                    break;

                case 'stop':
                    $this->stop($output);

                    break;
            }
        } catch (\EngineException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    /**
     * Start
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function start(OutputInterface $output)
    {
        $output->writeln('Starting daemon...');

        if (file_exists($this->pidfile)) {
            $output->writeln('<error>Daemon process is already running</error>');
            return null;
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            $output->writeln('<error>Could not fork</error>');
            return null;

        } elseif ($pid) {
            file_put_contents($this->pidfile, $pid);
            $output->writeln('Daemon started with PID ' . $pid);

        } else {
            $terminated = false;

            pcntl_signal(
                SIGTERM,
                function ($signo) use (&$terminated) {
                    if ($signo == SIGTERM) {
                        $terminated = true;
                    }
                }
            );

            while (!$terminated) {
                $this->executeOperation();

                pcntl_signal_dispatch();

                sleep(1);
            }

            $output->writeln('Daemon stopped');
        }
    }

    /**
     * Stop
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function stop(OutputInterface $output)
    {
        if (!file_exists($this->pidfile)) {
            $output->writeln('<error>PID file not found</error>');
        } else {
            $output->writeln('Stopping daemon ... ');
            $pid = file_get_contents($this->pidfile);

            posix_kill($pid, SIGTERM);
            pcntl_waitpid($pid, $status);

            $output->writeln('Daemon stopped with PID ' . $pid);

            unlink($this->pidfile);
        }
    }

    /**
     * Operation in infinite loop
     *
     * @return void
     */
    abstract protected function executeOperation();
}