<?php

declare(strict_types=1);

/**
 * Contains the command builder trait
 *
 * @copyright   Copyright (c) 2014-2020 Tristan Lins
 * @author      Tristan Lins
 * @author      Christian Schiffler
 * @author      Sven Baumann
 * @author      Attila Fulop
 * @license     MIT
 * @since       2014-03-16
 *
 */

namespace Konekt\GitPhp\Concerns;

use Konekt\GitPhp\GitException;
use Konekt\GitPhp\GitRepository;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Process;

trait BuildsCommand
{
    public GitRepository $repository;

    protected string $workingDirectory;

    protected array $arguments = [];

    protected ?string $output = null;

    protected bool $dryRun = false;

    public function __construct(GitRepository $repository)
    {
        $this->repository       = $repository;
        $this->workingDirectory = $repository->getRepositoryPath();
        $this->arguments[]      = $this->repository->getConfig()->getGitExecutablePath();

        $this->initializeProcessBuilder();
    }

    /**
     * Enable dry run. If dry run is enabled, the execute() method return the executed command.
     *
     * @return $this
     */
    public function enableDryRun()
    {
        $this->dryRun = true;

        return $this;
    }

    abstract protected function initializeProcessBuilder(): void;

    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @throws LogicException In case no arguments have been provided.
     */
    protected function buildProcess(): Process
    {
        if (!\count($this->arguments)) {
            throw new LogicException('You must add command arguments before the process can build.');
        }

        return new Process($this->arguments, $this->workingDirectory);
    }

    /**
     * @throws GitException When the command is executed the second time or could not be executed.
     */
    protected function run()
    {
        $process = $this->buildProcess();

        if ($this->output !== null) {
            throw new GitException(
                'Command cannot be executed twice',
                $process->getWorkingDirectory(),
                $process->getCommandLine(),
                $this->output,
                ''
            );
        }

        $this->repository->getConfig()->getLogger()->debug(
            \sprintf('Executing git command [%s] %s', $this->workingDirectory, $process->getCommandLine())
        );

        if ($this->dryRun) {
            return $process->getCommandLine();
        }

        $process->run();
        $this->output = $process->getOutput();
        $this->output = \rtrim($this->output, "\r\n");

        if (!$process->isSuccessful()) {
            throw GitException::createFromProcess('Could not execute git command', $process);
        }

        return $this->output;
    }
}
