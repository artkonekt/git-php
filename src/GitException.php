<?php

declare(strict_types=1);

/**
 * Exception thrown when execution of git failed
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

namespace Konekt\GitPhp;

use Symfony\Component\Process\Process;

class GitException extends \RuntimeException
{
    /** The working directory path */
    protected string $workingDirectory;

    /** The executed command line */
    protected string $commandLine;

    /** The git commands standard output */
    protected string $commandOutput;

    /** The git commands error output */
    protected string $errorOutput;

    public function __construct(
        string $message,
        ?string $workingDirectory,
        ?string $commandLine,
        ?string $commandOutput,
        ?string $errorOutput
    )
    {
        parent::__construct($message, 0, null);

        $this->workingDirectory = (string) $workingDirectory;
        $this->commandLine      = (string) $commandLine;
        $this->commandOutput    = (string) $commandOutput;
        $this->errorOutput      = (string) $errorOutput;
    }

    public function getWorkingDirectory(): string
    {
        return $this->workingDirectory;
    }

    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function getCommandOutput(): string
    {
        return $this->commandOutput;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    public static function createFromProcess(string $message, Process $process): self
    {
        return new static(
            \sprintf('%s [%s]', $message, $process->getCommandLine()) .
            PHP_EOL . \sprintf('work dir: %s', $process->getWorkingDirectory()) .
            PHP_EOL . $process->getErrorOutput(),
            $process->getWorkingDirectory(),
            $process->getCommandLine(),
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }
}
