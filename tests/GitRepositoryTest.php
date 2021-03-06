<?php

/**
 * This file is part of konekt/git-php and is a fork of the bit3/git-php project
 *
 * (c) Tristan Lins <tristan@lins.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/git-php
 * @author     Tristan Lins <tristan@lins.io>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Matthew Gamble <git@matthewgamble.net>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Attila Fulop <https://github.com/fulopattila122>
 * @copyright  2014-2018 Tristan Lins <tristan@lins.io>
 * @license    https://github.com/artkonekt/git-php/blob/master/LICENSE MIT
 * @link       https://github.com/artkonekt/git-php
 * @filesource
 */

namespace Konekt\GitPhp\Test;

use Konekt\GitPhp\GitRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * GIT repository unit tests.
 */
class GitRepositoryTest extends TestCase
{
    /**
     * @var string
     */
    protected $initializedRepositoryPath;

    /**
     * @var string
     */
    protected $uninitializedRepositoryPath;

    /**
     * @var GitRepository
     */
    protected $initializedGitRepository;

    /**
     * @var GitRepository
     */
    protected $uninitializedGitRepository;

    public function setUp(): void
    {
        $this->initializedRepositoryPath = \tempnam(\sys_get_temp_dir(), 'git_');
        \unlink($this->initializedRepositoryPath);
        \mkdir($this->initializedRepositoryPath);

        $this->uninitializedRepositoryPath = \tempnam(\sys_get_temp_dir(), 'git_');
        \unlink($this->uninitializedRepositoryPath);
        \mkdir($this->uninitializedRepositoryPath);

        $zip = new \ZipArchive();
        $zip->open(__DIR__ . DIRECTORY_SEPARATOR . 'git.zip');
        $zip->extractTo($this->initializedRepositoryPath);

        $this->initializedGitRepository   = new GitRepository($this->initializedRepositoryPath);
        $this->uninitializedGitRepository = new GitRepository($this->uninitializedRepositoryPath);
    }

    public function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->initializedRepositoryPath);
        $fs->remove($this->uninitializedRepositoryPath);

        unset($this->initializedRepositoryPath);
        unset($this->uninitializedRepositoryPath);
        unset($this->initializedGitRepository);
        unset($this->uninitializedGitRepository);
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::getRepositoryPath
     */
    public function testGetRepositoryPath()
    {
        $this->assertEquals(
            $this->initializedRepositoryPath,
            $this->initializedGitRepository->getRepositoryPath()
        );
        $this->assertEquals(
            $this->uninitializedRepositoryPath,
            $this->uninitializedGitRepository->getRepositoryPath()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::isInitialized
     */
    public function testIsInitialized()
    {
        $this->assertTrue(
            $this->initializedGitRepository->isInitialized()
        );
        $this->assertFalse(
            $this->uninitializedGitRepository->isInitialized()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::init
     * @covers \Konekt\GitPhp\Commands\InitCommandBuilder::execute
     */
    public function testInit()
    {
        $this->uninitializedGitRepository->init()->execute();

        $this->assertTrue(
            \is_dir($this->uninitializedRepositoryPath . DIRECTORY_SEPARATOR . '.git')
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::config
     * @covers \Konekt\GitPhp\Commands\ConfigCommandBuilder::execute
     * @covers \Konekt\GitPhp\Commands\ConfigCommandBuilder::get
     */
    public function testConfigGetOnInitializedRepository()
    {
        $this->assertEquals(
            'false',
            $this->initializedGitRepository->config()->file('local')->execute('core.bare')
        );
        $this->assertEquals(
            'CCA unittest',
            $this->initializedGitRepository->config()->file('local')->get('user.name')->execute()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::config
     * @covers \Konekt\GitPhp\Commands\ConfigCommandBuilder
     */
    public function testConfigGetOnUnitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->config()->file('local')->execute('core.bare');
        $this->uninitializedGitRepository->config()->file('local')->get('user.name')->execute();
    }

    public function testConfigSetOnInitializedRepository()
    {
        $this->initializedGitRepository->config()->file('local')->execute('user.name', 'CCA unittest 2');

        $process = new Process(['git', 'config', '--local', 'user.name'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            'CCA unittest 2',
            trim($process->getOutput())
        );
    }

    public function testConfigAddOnInitializedRepository()
    {
        $this->initializedGitRepository->config()->file('local')->add('user.name', 'CCA unittest 2')->execute();

        $process = new Process(
            ['git', 'config', '--local', '--get-all', 'user.name'],
            $this->initializedRepositoryPath
        );
        $process->run();

        $names = \explode("\n", $process->getOutput());
        $names = \array_map('trim', $names);
        $names = \array_filter($names);

        $this->assertEquals(
            ['CCA unittest', 'CCA unittest 2'],
            $names
        );
    }

    public function testConfigGetAllOnInitializedRepository()
    {
        $values = $this->initializedGitRepository->config()->file('local')->getAll('gitphp.test2')->execute();

        $values = \explode("\n", $values);
        $values = \array_map('trim', $values);
        $values = \array_filter($values);

        $this->assertEquals(
            ['aa123', 'ab234', 'ac345', 'bb234'],
            $values
        );

        $values = $this->initializedGitRepository->config()->file('local')->getAll('gitphp.test2', '^a.+3.+$')->execute();

        $values = \explode("\n", $values);
        $values = \array_map('trim', $values);
        $values = \array_filter($values);

        $this->assertEquals(
            ['ab234', 'ac345'],
            $values
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::getNames
     */
    public function testListRemotesOnInitializedRepository()
    {
        $this->assertEquals(
            ['local'],
            $this->initializedGitRepository->remote()->getNames()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::getNames
     */
    public function testListRemotesOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->remote()->getNames();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::branch
     * @covers \Konekt\GitPhp\Commands\BranchCommandBuilder::all
     * @covers \Konekt\GitPhp\Commands\BranchCommandBuilder::getNames
     */
    public function testListBranchesOnInitializedRepository()
    {
        $this->assertEquals(
            ['master'],
            $this->initializedGitRepository->branch()->getNames()
        );
        $this->assertEquals(
            ['master', 'remotes/local/master'],
            $this->initializedGitRepository->branch()->all()->getNames()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::branch
     * @covers \Konekt\GitPhp\Commands\BranchCommandBuilder::getNames
     */
    public function testListBranchesOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->branch()->getNames();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::describe
     * @covers \Konekt\GitPhp\Commands\DescribeCommandBuilder::tags
     * @covers \Konekt\GitPhp\Commands\DescribeCommandBuilder::all
     * @covers \Konekt\GitPhp\Commands\DescribeCommandBuilder::execute
     */
    public function testDescribeOnInitializedRepository()
    {
        $this->assertEquals(
            'annotated-tag-2-g8dcaf85',
            $this->initializedGitRepository->describe()->execute()
        );
        $this->assertEquals(
            'lightweight-tag-1-g8dcaf85',
            $this->initializedGitRepository->describe()->tags()->execute()
        );
        $this->assertEquals(
            'heads/master',
            $this->initializedGitRepository->describe()->all()->execute()
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::describe
     * @covers \Konekt\GitPhp\Commands\DescribeCommandBuilder::execute
     */
    public function testDescribeOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->describe()->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::setUrl
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteSetUrlOnInitializedRepository()
    {
        $this->initializedGitRepository->remote()->setUrl('local', $this->uninitializedRepositoryPath)->execute();

        $process = new Process(['git', 'config', 'remote.local.url'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            \trim($process->getOutput()),
            $this->uninitializedRepositoryPath
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::setUrl
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteSetUrlOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->remote()->setUrl('local', $this->initializedRepositoryPath)->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::setPushUrl
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteSetPushUrlOnInitializedRepository()
    {
        $this->initializedGitRepository->remote()->setPushUrl('local', $this->uninitializedRepositoryPath)->execute();

        $process = new Process(['git', 'config', 'remote.local.url'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            \trim($process->getOutput()),
            '/tmp/git'
        );

        $process = new Process(['git', 'config', 'remote.local.pushurl'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            \trim($process->getOutput()),
            $this->uninitializedRepositoryPath
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::setPushUrl
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteSetPushUrlOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->remote()->setPushUrl('local', $this->initializedRepositoryPath)->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::add
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteAddOnInitializedRepository()
    {
        $this->initializedGitRepository->remote()->add('origin', $this->uninitializedRepositoryPath)->execute();

        $process = new Process(['git', 'config', 'remote.origin.url'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            trim($process->getOutput()),
            $this->uninitializedRepositoryPath
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::remote
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::add
     * @covers \Konekt\GitPhp\Commands\RemoteCommandBuilder::execute
     */
    public function testRemoteAddOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->remote()->add('origin', $this->initializedRepositoryPath)->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::fetch
     * @covers \Konekt\GitPhp\Commands\FetchCommandBuilder::execute
     */
    public function testRemoteFetchOnInitializedRepository()
    {
        $process = new Process(
            ['git', 'remote', 'add', 'origin', $this->initializedRepositoryPath],
            $this->initializedRepositoryPath
        );
        $process->run();

        $this->initializedGitRepository->fetch()->execute();

        $process = new Process(['git', 'branch', '-a'], $this->initializedRepositoryPath);

        $process->run();

        $branches = explode("\n", $process->getOutput());
        $branches = array_map('trim', $branches);
        $branches = array_filter($branches);

        $this->assertTrue(
            \in_array('remotes/origin/master', $branches)
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::fetch
     * @covers \Konekt\GitPhp\Commands\FetchCommandBuilder::execute
     */
    public function testRemoteFetchOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->fetch()->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::checkout
     * @covers \Konekt\GitPhp\Commands\CheckoutCommandBuilder::execute
     */
    public function testCheckoutOnInitializedRepository()
    {
        $process = new Process(
            ['git', 'remote', 'add', 'origin', $this->initializedRepositoryPath],
            $this->initializedRepositoryPath
        );

        $process->run();

        $this->initializedGitRepository->checkout()->execute('6c42d7ba78e0e956bd4e25661a6c13d826ef590a');

        $process = new Process(['git', 'describe'], $this->initializedRepositoryPath);
        $process->run();

        $this->assertEquals(
            \trim($process->getOutput()),
            'annotated-tag'
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::checkout
     * @covers \Konekt\GitPhp\Commands\CheckoutCommandBuilder::execute
     */
    public function testCheckoutOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->checkout()->execute('foo');
    }

    public function testPushOnInitializedRepository()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::push
     * @covers \Konekt\GitPhp\Commands\PushCommandBuilder::execute
     */
    public function testPushOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->push()->execute('foo');
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::status
     * @covers \Konekt\GitPhp\Commands\StatusCommandBuilder::getStatus
     */
    public function testStatusOnInitializedRepository()
    {
        $status = $this->initializedGitRepository->status()->getStatus();

        $this->assertEquals(
            [
                'removed-but-staged.txt' => ['index' => 'A', 'worktree' => 'D'],
                'unknown-file.txt'       => ['index' => '?', 'worktree' => '?'],
            ],
            $status
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::status
     * @covers \Konekt\GitPhp\Commands\StatusCommandBuilder::getStatus
     */
    public function testStatusOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->status()->getStatus();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::add
     * @covers \Konekt\GitPhp\Commands\AddCommandBuilder::execute
     */
    public function testAddOnInitializedRepository()
    {
        $this->initializedGitRepository->add()->execute('unknown-file.txt');

        $process = new Process(['git', 'status', '-s'], $this->initializedRepositoryPath);
        $process->run();

        $status = \explode("\n", $process->getOutput());
        $status = \array_map('trim', $status);

        $this->assertTrue(
            \in_array('A  unknown-file.txt', $status)
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::add
     * @covers \Konekt\GitPhp\Commands\AddCommandBuilder::execute
     */
    public function testAddOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->add()->execute('unknown-file.txt');
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::rm
     * @covers \Konekt\GitPhp\Commands\RmCommandBuilder::execute
     */
    public function testRmOnInitializedRepository()
    {
        $this->initializedGitRepository->rm()->execute('existing-file.txt');

        $process = new Process(['git', 'status', '-s'], $this->initializedRepositoryPath);
        $process->run();

        $status = \explode("\n", $process->getOutput());
        $status = \array_map('trim', $status);

        $this->assertTrue(
            \in_array('D  existing-file.txt', $status)
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::rm
     * @covers \Konekt\GitPhp\Commands\RmCommandBuilder::execute
     */
    public function testRmOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->rm()->execute('existing-file.txt');
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::commit
     * @covers \Konekt\GitPhp\Commands\CommitCommandBuilder::message
     * @covers \Konekt\GitPhp\Commands\CommitCommandBuilder::execute
     */
    public function testCommitOnInitializedRepository()
    {
        $this->initializedGitRepository->commit()->message('Commit changes')->execute();

        $process = new Process(['git', 'status', '-s'], $this->initializedRepositoryPath);
        $process->run();

        $status = \explode("\n", $process->getOutput());
        $status = \array_map('trim', $status);
        $status = \array_filter($status);

        $this->assertEquals(
            [
                'D existing-file.txt',
                'D removed-but-staged.txt',
                '?? unknown-file.txt',
            ],
            $status
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::commit
     * @covers \Konekt\GitPhp\Commands\CommitCommandBuilder::message
     * @covers \Konekt\GitPhp\Commands\CommitCommandBuilder::execute
     */
    public function testCommitOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->commit()->message('Commit changes')->execute();
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::tag
     * @covers \Konekt\GitPhp\Commands\TagCommandBuilder::execute
     */
    public function testTagOnInitializedRepository()
    {
        $this->initializedGitRepository->tag()->execute('unit-test');

        $process = new Process(['git', 'tag'], $this->initializedRepositoryPath);
        $process->run();

        $tags = \explode("\n", $process->getOutput());
        $tags = \array_map('trim', $tags);
        $tags = \array_filter($tags);

        $this->assertTrue(
            \in_array('unit-test', $tags)
        );
    }

    /**
     * @covers \Konekt\GitPhp\GitRepository::tag
     * @covers \Konekt\GitPhp\Commands\TagCommandBuilder::execute
     */
    public function testTagOnUninitializedRepository()
    {
        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Konekt\GitPhp\GitException');
        } else {
            $this->expectException('Konekt\GitPhp\GitException');
        }

        $this->uninitializedGitRepository->tag()->execute('unit-test');
    }
}
