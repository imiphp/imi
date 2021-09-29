#!/usr/bin/env php
<?php

use app\Secrets;
use Github\Client;

require __DIR__ . '/vendor/autoload.php';

function execCMD(string $cmd, string $description = '', ?array &$result = null, ?callable $callback = null): void
{
    $result = [];
    echo '--begin--', \PHP_EOL;
    if ($description)
    {
        echo $description, ':', \PHP_EOL;
    }
    echo $cmd, \PHP_EOL;
    exec($cmd, $result, $resultCode);
    echo implode(\PHP_EOL, $result), \PHP_EOL;
    if (0 !== $resultCode && (null === $callback || !$callback($result, $resultCode)))
    {
        echo sprintf('cmd status code is %s', $resultCode), \PHP_EOL;
        exit($resultCode);
    }
    echo '--end--', \PHP_EOL;
}

/**
 * 根据最后一次处理的提交记录，获取commit列表，顺序从旧到新.
 */
function getCommitsFromLast(?string $lastCommit): array
{
    $commits = [];
    $result = null;
    execCMD('git show --stat=99999', '', $result);
    $commits[] = $lastHash = substr($result[0], 7, 40);
    if (null === $lastCommit)
    {
        execCMD('git log', '', $result);
    }
    else
    {
        execCMD('git log HEAD...' . $lastCommit, '', $result);
    }
    foreach ($result as $row)
    {
        if ('commit ' === substr($row, 0, 7))
        {
            $hash = substr($row, 7, 40);
            if ($hash !== $lastHash)
            {
                $commits[] = $hash;
            }
        }
    }

    return array_reverse(array_values($commits));
}

function getRepository(): string
{
    $content = getenv('GITHUB_REPOSITORY');
    if (!$content)
    {
        throw new \InvalidArgumentException(sprintf('Invalid GITHUB_REPOSITORY %s', $content));
    }

    return $content;
}

function getAccessToken(): string
{
    $content = getenv('IMI_ACCESS_TOKEN');
    if (!$content)
    {
        throw new \InvalidArgumentException(sprintf('Invalid IMI_ACCESS_TOKEN %s', $content));
    }

    return $content;
}

function getBranch(): string
{
    $content = getenv('GITHUB_REF');
    if (!$content)
    {
        throw new \InvalidArgumentException(sprintf('Invalid GITHUB_REF %s', $content));
    }

    [, $branch] = explode('refs/heads/', $content);

    return $branch;
}

function loadConfig(): array
{
    $content = getenv('SPLIT_CONFIG');
    if ($content)
    {
        $content = json_decode($content, true);
    }
    else
    {
        $content = [];
    }

    return $GLOBALS['config'] = $content;
}

function saveConfig(): void
{
    $client = new Client();
    $client->authenticate(getAccessToken(), null, Client::AUTH_ACCESS_TOKEN);

    $repository = explode('/', getRepository());

    $secrets = new Secrets($client);
    $publicKey = $secrets->publicKey(...$repository);

    $value = base64_encode(sodium_crypto_box_seal(json_encode($GLOBALS['config']), base64_decode($publicKey['key'])));

    $client->repository()->secrets()->update($repository[0], $repository[1], 'SPLIT_CONFIG', [
        'encrypted_value' => $value,
        'key_id'          => $publicKey['key_id'],
    ]);
}

chdir(__DIR__);

static $storeRepoMap = [
    'src/Components/swoole' => [
        'git@github.com:imiphp/imi-swoole',
    ],
    'src/Components/workerman' => [
        'git@github.com:imiphp/imi-workerman',
    ],
    'src/Components/fpm' => [
        'git@github.com:imiphp/imi-fpm',
    ],
    'src/Components/workerman-gateway' => [
        'git@github.com:imiphp/imi-workerman-gateway',
    ],
    'src/Components/access-control' => [
        'git@github.com:imiphp/imi-access-control',
    ],
    'src/Components/amqp' => [
        'git@github.com:imiphp/imi-amqp',
    ],
    'src/Components/apidoc' => [
        'git@github.com:imiphp/imi-apidoc',
    ],
    'src/Components/grpc' => [
        'git@github.com:imiphp/imi-grpc',
    ],
    'src/Components/hprose' => [
        'git@github.com:imiphp/imi-hprose',
    ],
    'src/Components/jwt' => [
        'git@github.com:imiphp/imi-jwt',
    ],
    'src/Components/kafka' => [
        'git@github.com:imiphp/imi-kafka',
    ],
    'src/Components/mqtt' => [
        'git@github.com:imiphp/imi-mqtt',
    ],
    'src/Components/queue' => [
        'git@github.com:imiphp/imi-queue',
    ],
    'src/Components/rate-limit' => [
        'git@github.com:imiphp/imi-rate-limit',
    ],
    'src/Components/rpc' => [
        'git@github.com:imiphp/imi-rpc',
    ],
    'src/Components/shared-memory'   => [
        'git@github.com:imiphp/imi-shared-memory.git',
    ],
    'src/Components/smarty' => [
        'git@github.com:imiphp/imi-smarty',
    ],
    'src/Components/snowflake' => [
        'git@github.com:imiphp/imi-snowflake',
    ],
    'src/Components/swoole-tracker' => [
        'git@github.com:imiphp/imi-swoole-tracker',
    ],
    'src/Components/pgsql' => [
        'git@github.com:imiphp/imi-pgsql',
    ],
    'src/Components/roadrunner' => [
        'git@github.com:imiphp/imi-roadrunner',
    ],
];

$mainRepoPath = dirname(__DIR__) . '/';

loadConfig();

// 主仓库
chdir($mainRepoPath);

$branch = getBranch();
$commits = getCommitsFromLast($GLOBALS['config'][$branch]['last_commit'] ?? null);

// 子仓库更新
foreach ($storeRepoMap as $name => $urls)
{
    $url = $urls[0];
    chdir(__DIR__);
    $repoName = basename($url, '.git');
    $repoPath = __DIR__ . '/' . $repoName . '/';
    if (is_dir($repoPath))
    {
        chdir($repoPath);
        execCMD('git reset --hard && git pull', '拉取' . $repoName);
    }
    else
    {
        chdir(__DIR__);
        execCMD('git clone ' . $url, '克隆' . $url);
        chdir($repoPath);
    }

    execCMD('git branch -a', '分支列表', $branches);
    $noBranch = !$branch;
    if ($branch)
    {
        if (!in_array('* ' . $branch, $branches))
        {
            if (in_array('  remotes/origin/' . $branch, $branches))
            {
                execCMD('git checkout -b ' . $branch . ' remotes/origin/' . $branch);
            }
            elseif (in_array('  ' . $branch, $branches))
            {
                execCMD('git checkout ' . $branch);
            }
            else
            {
                execCMD('git checkout -b ' . $branch);
            }
        }
    }

    $len = count($urls);
    for ($i = 1; $i < $len; ++$i)
    {
        execCMD('git remote remove r' . $i . ' ' . $urls[$i], '删除远端' . $i);
        execCMD('git remote add r' . $i . ' ' . $urls[$i], '增加远端' . $i);
    }
    $path = $name . '/';
    $pathLen = strlen($path);
    foreach ($commits as $commit)
    {
        chdir($mainRepoPath);
        execCMD('git show ' . $commit . ' -s --format=%cn', '', $result);
        $author = $result[0];
        execCMD('git show ' . $commit . ' -s --format=%ad', '', $result);
        $date = $result[0];
        execCMD('git show ' . $commit . ' -s --format=%s', '', $result);
        $message = $result[0];
        execCMD('git --no-pager show ' . $commit . ' --stat=99999', '提交记录', $result);

        $needCommit = false;
        foreach ($result as $row)
        {
            if (!preg_match('/ (.+?)\s+\| /', $row, $matches))
            {
                continue;
            }

            if (preg_match('/(.+)\s+=>\s+(.+)/', $matches[1], $matches2))
            {
                // 重命名
                if ('}' === $matches2[2][-1])
                {
                    // 同目录下重命名
                    $from = str_replace('{', '', $matches2[1]);
                    $to = dirname($from) . '/' . substr($matches2[2], 0, -1);
                }
                else
                {
                    $from = $matches2[1];
                    $to = $matches2[2];
                }
                if ($path === substr($from, 0, $pathLen))
                {
                    $repoFilePath = $repoPath . substr($from, $pathLen);
                    if (is_file($repoFilePath))
                    {
                        unlink($repoFilePath);
                        $needCommit = true;
                    }
                }
                if ($path === substr($to, 0, $pathLen))
                {
                    $repoFilePath = $repoPath . substr($to, $pathLen);
                    $originFileName = $mainRepoPath . $to;
                    if (is_file($originFileName))
                    {
                        $dir = dirname($repoFilePath);
                        if (!is_dir($dir))
                        {
                            mkdir($dir, 0777, true);
                        }
                        file_put_contents($repoFilePath, file_get_contents($originFileName));
                        chdir($repoPath);
                        execCMD('git add ' . $repoFilePath, 'git add');
                        execCMD('git update-index --chmod=' . (is_executable($originFileName) ? '+' : '-') . 'x ' . $repoFilePath);
                        $needCommit = true;
                    }
                }
            }
            else
            {
                // 文件修改
                $fileName = $matches[1];
                if ($path === substr($fileName, 0, $pathLen))
                {
                    $repoFilePath = $repoPath . substr($fileName, $pathLen);
                    $originFileName = $mainRepoPath . $fileName;
                    if (is_file($originFileName))
                    {
                        $dir = dirname($repoFilePath);
                        if (!is_dir($dir))
                        {
                            mkdir($dir, 0777, true);
                        }
                        file_put_contents($repoFilePath, file_get_contents($originFileName));
                        chdir($repoPath);
                        execCMD('git add ' . $repoFilePath, 'git add');
                        execCMD('git update-index --chmod=' . (is_executable($originFileName) ? '+' : '-') . 'x ' . $repoFilePath);
                        $needCommit = true;
                    }
                    else
                    {
                        if (is_file($repoFilePath))
                        {
                            unlink($repoFilePath);
                            $needCommit = true;
                        }
                    }
                }
            }
        }
        if (!$needCommit)
        {
            continue;
        }
        chdir($mainRepoPath);
        $authorName = trim(shell_exec('git show ' . $commit . ' -s --format=%cn'));
        $authorEmail = trim(shell_exec('git show ' . $commit . ' -s --format=%ce'));

        chdir($repoPath);
        if ($noBranch)
        {
            execCMD('git branch -M ' . $branch, '');
        }
        execCMD('git status -s', '', $result);
        if ($result)
        {
            execCMD('git config user.name "' . $authorName . '" && git config user.email "' . $authorEmail . '" && git commit --author "' . $author . ' <' . $authorEmail . '>" --date "' . $date . '" -am \'' . $message . '\'', 'git commit', $result, function (array $result, int $resultCode) {
                return false !== strpos(implode(\PHP_EOL, $result), 'nothing to commit');
            });
        }
    }
    chdir($repoPath);
    foreach ($urls as $i => $url)
    {
        if (0 === $i)
        {
            execCMD('git push --set-upstream origin ' . $branch, '推送');
        }
        else
        {
            execCMD('git push --set-upstream r' . $i . ' ' . $branch, '推送' . $i);
        }
    }
}

$GLOBALS['config'][$branch]['last_commit'] = end($commits);

saveConfig();
