<?php

namespace Imi\Phar;

use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function file_exists;
use function trim;

class Helper
{
    /**
     * @param string          $path
     * @param OutputInterface $output
     * @return array{hash: string, branch: string, tag: string}
     */
    public static function resolveGitInfo(string $path, OutputInterface $output): array
    {
        $result = [
            'hash'   => null,
            'branch' => null,
            'tag'    => null,
        ];

        try {
            $result['hash'] = self::resolveGitHash($path);
        } catch (RuntimeException $exception) {
            $output->writeln("<comment>warning</comment>: git hash get failed, {$exception->getMessage()}");
        }
        try {
            $result['branch'] = self::resolveGitBranch($path);
        } catch (RuntimeException $exception) {
            $output->writeln("<comment>warning</comment>: git branch get failed, {$exception->getMessage()}");
        }
        try {
            $result['tag'] = self::resolveGitTag($path);
        } catch (RuntimeException $exception) {
            $output->writeln("<comment>warning</comment>: git tag get failed, {$exception->getMessage()}",);
        }

        return $result;
    }

    /**
     * @link https://github.com/box-project/box/blob/e2cbc2424c0c4b97b626653c7f8ff8029282b9aa/src/Configuration/Configuration.php#L2178-L2187
     */
    private static function resolveGitHash(string $path): string
    {
        return self::runGitCommand('git log --pretty="%H" -n1 HEAD', $path);
    }

    private static function resolveGitBranch(string $path): string
    {
        return self::runGitCommand('git symbolic-ref --short HEAD', $path);
    }

    /**
     * @link https://github.com/box-project/box/blob/e2cbc2424c0c4b97b626653c7f8ff8029282b9aa/src/Configuration/Configuration.php#L2206-L2209
     */
    private static function resolveGitTag(string $path): string
    {
        return self::runGitCommand('git describe --tags HEAD', $path);
    }

    /**
     * Runs a Git command on the repository.
     *
     * @return string The trimmed output from the command
     *
     * @link https://github.com/box-project/box/blob/e2cbc2424c0c4b97b626653c7f8ff8029282b9aa/src/Configuration/Configuration.php#L2227-L2246
     */
    private static function runGitCommand(string $command, string $path): string
    {
        $process = Process::fromShellCommandline($command, $path);
        $process->run();

        if ($process->isSuccessful()) {
            return trim($process->getOutput());
        }

        throw new RuntimeException(
            sprintf(
                'Unable to execute git command: %s',
                trim($process->getErrorOutput()),
            ),
            0,
            new ProcessFailedException($process),
        );
    }
}
