<?php

declare(strict_types=1);

/**
 * Contains the Shareable configuration for git repositories class
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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class GitConfig
{
    /** The path to the git executable. */
    private string $gitExecutablePath = 'git';

    /** ID of the GPG certificate to sign commits. */
    private ?string $signCommitUser = null;

    /** ID of the GPG certificate to sign tags. */
    private ?string $signTagUser = null;

    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function setGitExecutablePath(string $gitExecutablePath): GitConfig
    {
        $this->gitExecutablePath = (string) $gitExecutablePath;

        return $this;
    }

    public function getGitExecutablePath(): string
    {
        return $this->gitExecutablePath;
    }

    public function enableSignCommits(string $signUser): GitConfig
    {
        $this->signCommitUser = (string) $signUser;

        return $this;
    }

    public function disableSignCommits(): GitConfig
    {
        $this->signCommitUser = null;

        return $this;
    }

    public function isSignCommitsEnabled(): bool
    {
        return (bool) $this->signCommitUser;
    }

    /**
     * Get the id of the GPG certificate to sign commits with.
     */
    public function getSignCommitUser(): ?string
    {
        return $this->signCommitUser;
    }

    public function enableSignTags(string $signUser): GitConfig
    {
        $this->signTagUser = (string) $signUser;

        return $this;
    }

    public function disableSignTags(): GitConfig
    {
        $this->signTagUser = null;

        return $this;
    }

    public function isSignTagsEnabled(): bool
    {
        return (bool) $this->signTagUser;
    }

    /**
     * Get the id of the GPG certificate to sign tags with.
     */
    public function getSignTagUser(): ?string
    {
        return $this->signTagUser;
    }

    public function setLogger(LoggerInterface $logger): GitConfig
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
