<?php

declare(strict_types=1);

/**
 * Contains the GIT repository adapter class
 *
 * @copyright   Copyright (c) 2014-2020 Tristan Lins
 * @author      Tristan Lins
 * @author      Christian Schiffler
 * @author      David Molineus
 * @author      Aaron Rubin
 * @author      Matthew Gamble
 * @author      Ahmad Marzouq
 * @author      Sven Baumann
 * @author      Attila Fulop
 * @license     MIT
 * @since       2014-03-16
 *
 */

namespace Konekt\GitPhp;

use Konekt\GitPhp\Command\AddCommandBuilder;
use Konekt\GitPhp\Command\BranchCommandBuilder;
use Konekt\GitPhp\Command\CheckoutCommandBuilder;
use Konekt\GitPhp\Command\CloneCommandBuilder;
use Konekt\GitPhp\Command\CommitCommandBuilder;
use Konekt\GitPhp\Command\ConfigCommandBuilder;
use Konekt\GitPhp\Command\DescribeCommandBuilder;
use Konekt\GitPhp\Command\FetchCommandBuilder;
use Konekt\GitPhp\Command\InitCommandBuilder;
use Konekt\GitPhp\Command\LogCommandBuilder;
use Konekt\GitPhp\Command\LsRemoteCommandBuilder;
use Konekt\GitPhp\Command\MergeCommandBuilder;
use Konekt\GitPhp\Command\PullCommandBuilder;
use Konekt\GitPhp\Command\PushCommandBuilder;
use Konekt\GitPhp\Command\RemoteCommandBuilder;
use Konekt\GitPhp\Command\ResetCommandBuilder;
use Konekt\GitPhp\Command\RevParseCommandBuilder;
use Konekt\GitPhp\Command\RmCommandBuilder;
use Konekt\GitPhp\Command\ShortLogCommandBuilder;
use Konekt\GitPhp\Command\ShowCommandBuilder;
use Konekt\GitPhp\Command\StashCommandBuilder;
use Konekt\GitPhp\Command\StatusCommandBuilder;
use Konekt\GitPhp\Command\TagCommandBuilder;

class GitRepository
{
    /** The path to the git repository */
    public string $repositoryPath;

    /** The shared git configuration */
    public GitConfig $config;

    public function __construct(?string $repositoryPath, GitConfig $config = null)
    {
        $this->repositoryPath = (string) $repositoryPath;
        $this->config         = null === $config ? new GitConfig() : $config;
    }

    public function getRepositoryPath(): string
    {
        return $this->repositoryPath;
    }

    public function getConfig(): GitConfig
    {
        return $this->config;
    }

    public function isInitialized(): bool
    {
        return \is_dir($this->repositoryPath . DIRECTORY_SEPARATOR . '.git');
    }

    public function init(): InitCommandBuilder
    {
        return new InitCommandBuilder($this);
    }

    public function cloneRepository(): CloneCommandBuilder
    {
        return new CloneCommandBuilder($this);
    }

    public function config(): ConfigCommandBuilder
    {
        return new ConfigCommandBuilder($this);
    }

    public function remote(): RemoteCommandBuilder
    {
        return new RemoteCommandBuilder($this);
    }

    public function branch(): BranchCommandBuilder
    {
        return new BranchCommandBuilder($this);
    }

    public function revParse(): RevParseCommandBuilder
    {
        return new RevParseCommandBuilder($this);
    }

    public function describe(): DescribeCommandBuilder
    {
        return new DescribeCommandBuilder($this);
    }

    public function reset(): ResetCommandBuilder
    {
        return new ResetCommandBuilder($this);
    }

    public function checkout(): CheckoutCommandBuilder
    {
        return new CheckoutCommandBuilder($this);
    }

    public function push(): PushCommandBuilder
    {
        return new PushCommandBuilder($this);
    }

    public function fetch(): FetchCommandBuilder
    {
        return new FetchCommandBuilder($this);
    }

    public function status(): StatusCommandBuilder
    {
        return new StatusCommandBuilder($this);
    }

    public function add(): AddCommandBuilder
    {
        return new AddCommandBuilder($this);
    }

    public function rm(): RmCommandBuilder
    {
        return new RmCommandBuilder($this);
    }

    public function commit(): CommitCommandBuilder
    {
        return new CommitCommandBuilder($this);
    }

    public function tag(): TagCommandBuilder
    {
        return new TagCommandBuilder($this);
    }

    public function show(): ShowCommandBuilder
    {
        return new ShowCommandBuilder($this);
    }

    public function log(): LogCommandBuilder
    {
        return new LogCommandBuilder($this);
    }

    public function shortLog(): ShortLogCommandBuilder
    {
        return new ShortLogCommandBuilder($this);
    }

    public function lsRemote(): LsRemoteCommandBuilder
    {
        return new LsRemoteCommandBuilder($this);
    }

    public function merge(): MergeCommandBuilder
    {
        return new MergeCommandBuilder($this);
    }

    public function pull(): PullCommandBuilder
    {
        return new PullCommandBuilder($this);
    }

    public function stash(): StashCommandBuilder
    {
        return new StashCommandBuilder($this);
    }
}
