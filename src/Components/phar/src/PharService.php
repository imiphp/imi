<?php

declare(strict_types=1);

namespace Imi\Phar;

use Composer\InstalledVersions;
use Phar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use function array_keys;
use function array_map;
use function var_dump;
use function var_export;

class PharService
{
    protected OutputInterface $output;

    protected string $outputPhar;
    protected string $baseDir = '';
    protected array  $dirs = [];
    protected array  $files = [];
    protected array  $excludeDirs = [];
    protected array  $excludeFiles = [];
    protected array  $finder = [];
    protected int    $compression = \Phar::NONE;

    /**
     * @var bool|Finder
     */
    protected $vendorScan = true;
    /**
     * @var array{hash: string, branch: string, tag: string}|null
     */
    protected ?array $gitInfo = null;
    protected bool $dumpGitInfo;

    protected int $buildTime;

    public function __construct(OutputInterface $output, string $baseDir, array $config)
    {
        $this->output = $output;
        $this->baseDir = $baseDir;

        $this->outputPhar = $config['output'] ?? 'build/imi.phar';
        if (!str_starts_with($this->outputPhar, '/') && !str_starts_with($this->outputPhar, '\\'))
        {
            $this->outputPhar = getcwd() . \DIRECTORY_SEPARATOR . $this->outputPhar;
        }

        $this->dirs = $config['dirs'] ?? [];
        $this->files = $config['files'] ?? [];
        $this->excludeDirs = $config['excludeDirs'] ?? [];
        $this->excludeFiles = $config['excludeFiles'] ?? [];
        $this->finder = $config['finder'] ?? [];

        // 暂时禁止压缩 $config['compression']
        $this->compression = \Phar::NONE;

        $this->buildTime = time();
        $this->dumpGitInfo = $config['dumpGitInfo'] ?? true;
    }

    public function build(string $container)
    {
        $outputPhar = $this->outputPhar;
        $outputDir = \dirname($outputPhar);
        if (!is_dir($outputDir))
        {
            mkdir($outputDir, 0755, true);
        }

        if (file_exists($outputPhar))
        {
            unlink($outputPhar);
        }

        if (!$this->dumpGitInfo || !file_exists($this->baseDir . \DIRECTORY_SEPARATOR . '.git'))
        {
            $this->output->writeln('dump git info: <comment>not support</comment>');
        }
        else
        {
            $this->output->writeln('dump git info: <info>support</info>');
            $this->gitInfo = Helper::resolveGitInfo($this->baseDir, $this->output);
            foreach ($this->gitInfo as $key => $value)
            {
                $this->output->writeln(sprintf('  > git %s: %s', $key, $value ?? 'null'));
            }
        }

        $this->output->writeln(sprintf('build date: <info>%s</info>', date(\DATE_ATOM, $this->buildTime)));

        $phar = new Phar($outputPhar, 0, 'imi.phar');
        // todo 支持私钥签名
        $phar->setSignatureAlgorithm(Phar::SHA256);

        $this->output->writeln('add files...');

        $phar->startBuffering();
        // filesProviderAggregate
        if ($this->dirs)
        {
            $phar->buildFromIterator($this->filesProvider(), $this->baseDir);
        }
        if (false !== $this->vendorScan)
        {
            $phar->buildFromIterator($this->vendorProvider(), $this->baseDir);
        }
        if ($this->finder)
        {
            $phar->buildFromIterator($this->finderProvider(), $this->baseDir);
        }
        $phar->addFile(__DIR__ . '/phar_init.php', '__stub_init.php');

        $this->output->writeln('add files done');

        $phar->setStub($this->buildStud($container));

        $phar->stopBuffering();

        $this->output->writeln('output phar file: ' . $this->outputPhar);

        if (\Phar::NONE !== $this->compression)
        {
            $phar->compressFiles($this->compression);
        }
    }

    public function checkContainer(string $container): bool
    {
        if (!\in_array($container, Constant::CONTAINER_SET))
        {
            $this->output->writeln('invalid container value');

            return false;
        }

        $package = Constant::CONTAINER_PACKAGE[$container];

        if (InstalledVersions::isInstalled($package))
        {
            return true;
        }

        $this->output->writeln("<error>container {$container} requires package {$package}.</error>");

        return false;
    }

    protected function buildGitInfoCode(): string
    {
        if (null === $this->gitInfo)
        {
            return '';
        }

        return sprintf(
            <<<PHP
            \define('IMI_PHAR_BUILD_GIT_HASH', %s);
            \define('IMI_PHAR_BUILD_GIT_BRANCH', %s);
            \define('IMI_PHAR_BUILD_GIT_TAG', %s);
            PHP,
            var_export($this->gitInfo['hash'], true),
            var_export($this->gitInfo['branch'], true),
            var_export($this->gitInfo['tag'], true),
        );
    }

    protected function buildStud(string $container): string
    {
        // none
        $bootstrapFile = Constant::CONTAINER_BOOTSTRAP[$container];

        $buildDate = date(\DATE_ATOM, $this->buildTime);

        $gitInfoCode = $this->buildGitInfoCode();

        // todo 支持定义覆盖 bootstrap 入口

        return <<<PHP
        #!/usr/bin/env php
        <?php

        \define('IMI_PHAR_BUILD_TIME', {$this->buildTime});
        \define('IMI_PHAR_BUILD_DATE', '{$buildDate}');
        {$gitInfoCode}

        Phar::mapPhar('imi.phar');
        require 'phar://imi.phar/__stub_init.php';

        \$main = require IMI_APP_ROOT . '/{$bootstrapFile}';
        \$main();
        __HALT_COMPILER();
        PHP;
    }

    protected function filesProvider(): \Generator
    {
        $finder = (new Finder())
            ->files()
            ->in(array_map(fn ($dir) => $this->baseDir . \DIRECTORY_SEPARATOR . $dir, $this->dirs))
            ->ignoreVCS(true);

        $finder->notName(Constant::CFG_FILE_NAME);

        $this->setBaseFilter($finder);

        if ($this->excludeDirs)
        {
            $finder->exclude($this->excludeDirs);
        }
        if ($this->excludeFiles)
        {
            $finder->notName($this->excludeFiles);
        }

        foreach ($finder as $filename => $_)
        {
            // var_dump("> {$filename}");
            yield $filename;
        }

        foreach ($this->files as $file)
        {
            $filename = $this->baseDir . \DIRECTORY_SEPARATOR . $file;
            // var_dump("> {$filename}");
            if (!is_file($filename))
            {
                continue;
            }
            yield $filename;
        }
    }

    protected function vendorProvider(): \Generator
    {
        if ($this->vendorScan instanceof Finder)
        {
            yield from $this->vendorScan;

            return;
        }

        $finder = (new Finder())
            ->files()
            ->in($this->baseDir . \DIRECTORY_SEPARATOR . 'vendor')
            ->ignoreVCS(true);

        $finder->notName(['/LICENSE|.*\\.md|.*\\.dist|Makefile/']);
        $finder->exclude([
            'doc',
            'test',
            'test_old',
            'tests',
            'Tests',
            'vendor-bin',
            'vendor/bin',
        ]);

        foreach ($finder as $filename => $_)
        {
            // var_dump("> {$filename}");
            yield $filename;
        }
    }

    protected function finderProvider(): \Generator
    {
        foreach ($this->finder as $finder)
        {
            if ($finder instanceof Finder)
            {
                yield from $finder;
            }
        }
    }

    protected function setBaseFilter(Finder $finder): void
    {
        // https://github.com/box-project/box/blob/e2cbc2424c0c4b97b626653c7f8ff8029282b9aa/src/Configuration/Configuration.php#L1478
        $finder
            // Remove build files
            ->notName('composer.json')
            ->notName('composer.lock')
            ->notName('Makefile')
            ->notName('Vagrantfile')
            ->notName('phpstan*.neon*')
            ->notName('infection*.json*')
            ->notName('humbug*.json*')
            ->notName('easy-coding-standard.neon*')
            ->notName('phpbench.json*')
            ->notName('phpcs.xml*')
            ->notName('psalm.xml*')
            ->notName('scoper.inc*')
            ->notName('box*.json*')
            ->notName('phpdoc*.xml*')
            ->notName('codecov.yml*')
            ->notName('Dockerfile')
            ->exclude('build')
            ->exclude('dist')
            ->exclude('example')
            ->exclude('examples')
            // Remove documentation
            ->notName('*.md')
            ->notName('*.rst')
            ->notName('/^readme((?!\.php)(\..*+))?$/i')
            ->notName('/^upgrade((?!\.php)(\..*+))?$/i')
            ->notName('/^contributing((?!\.php)(\..*+))?$/i')
            ->notName('/^changelog((?!\.php)(\..*+))?$/i')
            ->notName('/^authors?((?!\.php)(\..*+))?$/i')
            ->notName('/^conduct((?!\.php)(\..*+))?$/i')
            ->notName('/^todo((?!\.php)(\..*+))?$/i')
            ->exclude('doc')
            ->exclude('docs')
            ->exclude('documentation')
            // Remove backup files
            ->notName('*~')
            ->notName('*.back')
            ->notName('*.swp')
            // Remove tests
            ->notName('*Test.php')
            ->exclude('test')
            ->exclude('Test')
            ->exclude('tests')
            ->exclude('Tests')
            ->notName('/phpunit.*\.xml(.dist)?/')
            ->notName('/behat.*\.yml(.dist)?/')
            ->exclude('spec')
            ->exclude('specs')
            ->exclude('features')
            // Remove CI config
            ->exclude('travis')
            ->notName('travis.yml')
            ->notName('appveyor.yml')
            ->notName('build.xml*');
    }
}
