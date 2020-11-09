# Run Git Commands from PHP

[![Travis Build Status](https://img.shields.io/travis/artkonekt/git-php.svg?style=flat-square)](https://travis-ci.com/artkonekt/git-php)
[![Packagist Stable Version](https://img.shields.io/packagist/v/konekt/git-php.svg?style=flat-square&label=stable)](https://packagist.org/packages/konekt/git-php)
[![Packagist downloads](https://img.shields.io/packagist/dt/konekt/git-php.svg?style=flat-square)](https://packagist.org/packages/konekt/git-php)
[![StyleCI](https://styleci.io/repos/311014751/shield?branch=master)](https://styleci.io/repos/311014751)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

> This is a fork of the original [bit3/git-php](https://github.com/bit3/git-php) repository at version 1.5.0

## GIT Wrapper for PHP

This is a lightweight wrapper, providing the git commands in PHP.

### Examples

The API use command builders, that allow you to build a command and execute it one time.

The main synopsis is:

```php
$git->command()->option()->execute();
```

`$git->command()` will create a new command, `*->option()` will add an option to the command and
`*->execute()` will finally execute the command.

The naming of commands and options follow the git naming. If you search for documentation of a specific command
or option, just look into the git documentation. You will find the command/option there.

#### init a new git repository

```php
use Konekt\GitPhp\GitRepository;

$directory = '/path/to/git/target/directory';

$git = new GitRepository($directory);
$git->init()->execute();
```

#### clone a git repository

The `clone` command is named `cloneRepository()` because `clone` is a reserved word in PHP.

```php
use Konekt\GitPhp\GitRepository;

$directory = '/path/to/git/target/directory';

$git = new GitRepository($directory);
$git->cloneRepository()->execute();
```

#### describe

```php
$annotatedTag   = $git->describe()->execute();
$lightweightTag = $git->describe()->tags()->execute();
$recentRef      = $git->describe()->all()->execute();
```

#### set remote fetch url

```php
$git->remote()
    ->setUrl('origin', 'git@github.com:bit3/git-php.git')
    ->execute();
```

#### set remote push url

```php
$git->remote()
    ->setPushUrl('origin', 'git@github.com:bit3/git-php.git')
    ->execute();
```

#### add new remote

```php
$git->remote()
    ->add('github', 'git@github.com:bit3/git-php.git')
    ->execute();
```

#### fetch remote objects

```php
$git->fetch()->execute('github');
```

#### checkout

```php
$git->checkout()->execute('hotfix/1.2.3');
```

#### checkout specific path

```php
$git->checkout()->execute('hotfix/1.2.3', '/fileA', '/fileB', '/dir/fileC');
```

#### push objects

```php
$git->push()->execute('github', 'hotfix/1.2.3');
```

#### add file to staging index

```php
$git->add()->execute('file/to/add.ext');
```

#### remove file

```php
$git->rm()->execute('file/to/remove.ext');
```

#### commit changes

```php
$git->commit()->message('Commit message')->execute();
```

#### create a tag

```php
$git->tag()->execute('v1.2.3');
```

### Convenience and shortcut methods

#### list remotes

```php
$remotes = $git->remote()->getNames();

// array(
//     'origin',
//     'composer',
// )
```

#### list branches

```php
$remotes = $git->branch()->getNames();

// array(
//     'master',
//     'hotfix/1.2.3',
// )
```

#### list remote tracking branches

```php
$remotes = $git->branch()->remotes()->->getNames();

// array(
//     'origin/master',
//     'origin/hotfix/1.2.3',
//     'origin/release/4.5.6',
// )
```

#### list branches including remote tracking branches

```php
$remotes = $git->branch()->all()->->getNames();

// array(
//     'master',
//     'hotfix/1.2.3',
//     'remotes/origin/master',
//     'remotes/origin/hotfix/1.2.3',
//     'remotes/origin/release/4.5.6',
// )
```

#### get modification status

```php
$status = $git->status()->getStatus();

// array(
//     'existing-file.txt'      => array('index' => 'D',   'worktree' => false),
//     'removed-but-staged.txt' => array('index' => 'D',   'worktree' => 'A'),
//     'staged-file.txt'        => array('index' => false, 'worktree' => 'A'),
//     'unknown-file.txt'       => array('index' => '?',   'worktree' => '?'),
// )
```

#### get index modification status

```php
$status = $git->status()->getIndexStatus();

// array(
//     'existing-file.txt'      => 'D',
//     'removed-but-staged.txt' => 'D',
//     'staged-file.txt'        => false,
//     'unknown-file.txt'       => '?',
// )
```

#### get worktree modification status

```php
$status = $git->status()->getWorkTreeStatus();

// array(
//     'existing-file.txt'      => 'worktree' => false,
//     'removed-but-staged.txt' => 'worktree' => 'A',
//     'staged-file.txt'        => 'worktree' => 'A',
//     'unknown-file.txt'       => 'worktree' => '?',
// )
```
