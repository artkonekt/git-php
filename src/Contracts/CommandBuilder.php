<?php

declare(strict_types=1);

/**
 * Contains the command builder interface
 *
 * @copyright   Copyright (c) 2014-2020 Tristan Lins
 * @author      Tristan Lins
 * @author      Christian Schiffler
 * @author      Attila Fulop
 * @license     MIT
 * @since       2014-03-16
 *
 */

namespace Konekt\GitPhp\Contracts;

interface CommandBuilder
{
    public function getOutput(): ?string;

    public function enableDryRun();
}
