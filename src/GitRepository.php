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

use Konekt\GitPhp\Commands\AddCommandBuilder;
use Konekt\GitPhp\Commands\BranchCommandBuilder;
use Konekt\GitPhp\Commands\CheckoutCommandBuilder;
use Konekt\GitPhp\Commands\CloneCommandBuilder;
use Konekt\GitPhp\Commands\CommitCommandBuilder;
use Konekt\GitPhp\Commands\ConfigCommandBuilder;
use Konekt\GitPhp\Commands\DescribeCommandBuilder;
use Konekt\GitPhp\Commands\FetchCommandBuilder;
use Konekt\GitPhp\Commands\InitCommandBuilder;
use Konekt\GitPhp\Commands\LogCommandBuilder;
use Konekt\GitPhp\Commands\LsRemoteCommandBuilder;
use Konekt\GitPhp\Commands\MergeCommandBuilder;
use Konekt\GitPhp\Commands\PullCommandBuilder;
use Konekt\GitPhp\Commands\PushCommandBuilder;
use Konekt\GitPhp\Commands\RemoteCommandBuilder;
use Konekt\GitPhp\Commands\ResetCommandBuilder;
use Konekt\GitPhp\Commands\RevParseCommandBuilder;
use Konekt\GitPhp\Commands\RmCommandBuilder;
use Konekt\GitPhp\Commands\ShortLogCommandBuilder;
use Konekt\GitPhp\Commands\ShowCommandBuilder;
use Konekt\GitPhp\Commands\StashCommandBuilder;
use Konekt\GitPhp\Commands\StatusCommandBuilder;
use Konekt\GitPhp\Commands\TagCommandBuilder;

class GitRepository
{
    /** The path to the git repository */
    public string $repositoryPath;

    /** The shared git configuration */
    public GitConfig $config;

    public static function in(string $repositoryPath): self
    {
        return new self($repositoryPath);
    }

    public function usingConfig(GitConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

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
