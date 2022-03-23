<?php

declare(strict_types=1);

namespace Imi;

use Composer\InstalledVersions;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Container;
use Imi\Bean\Scanner;
use Imi\Cli\ImiCommand;
use Imi\Core\App\Contract\IApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Util\Imi;
use function substr;

class App
{
    /**
     * 应用命名空间.
     */
    private static string $namespace = '';

    /**
     * 容器.
     */
    private static Container $container;

    /**
     * 框架是否已初始化.
     */
    private static bool $isInited = false;

    /**
     * 当前是否为调试模式.
     */
    private static bool $isDebug = false;

    /**
     * 上下文集合.
     */
    private static array $context = [];

    /**
     * 只读上下文键名列表.
     */
    private static array $contextReadonly = [];

    /**
     * imi 版本号.
     */
    private static ?string $imiVersion = null;

    /**
     * imi 版本引用 Hash.
     */
    private static ?string $imiVersionReference = null;

    /**
     * App 实例对象
     */
    private static IApp $app;

    private function __construct()
    {
    }

    /**
     * 框架服务运行入口.
     *
     * @param string             $namespace 应用命名空间
     * @param class-string<IApp> $app
     */
    public static function run(string $namespace, string $app, ?callable $callback = null): void
    {
        /** @var IApp $appInstance */
        $appInstance = self::$app = new $app($namespace);
        self::initFramework($namespace);
        // 加载配置
        $appInstance->loadConfig();
        // 加载入口
        $appInstance->loadMain();
        Event::trigger('IMI.LOAD_CONFIG');
        // 加载运行时
        $result = $appInstance->loadRuntime();
        if (LoadRuntimeResult::NONE === $result)
        {
            // 扫描 imi 框架
            Scanner::scanImi();
        }
        if (!($result & LoadRuntimeResult::APP_LOADED))
        {
            // 扫描组件
            Scanner::scanVendor();
            // 扫描项目
            Scanner::scanApp();
        }
        Event::trigger('IMI.LOAD_RUNTIME');
        // 初始化
        $appInstance->init();
        // 注册错误日志
        // @phpstan-ignore-next-line
        self::getBean('ErrorLog')->register();
        Event::trigger('IMI.APP_RUN');
        // 运行
        if ($callback)
        {
            $callback();
        }
        else
        {
            $appInstance->run();
        }
    }

    /**
     * 框架初始化.
     */
    public static function initFramework(string $namespace): void
    {
        \define('IMI_PATH', __DIR__);
        // 项目命名空间
        self::$namespace = $namespace;
        // 容器类
        self::$container = new Container();
        // 注解管理器初始化
        AnnotationManager::init();
        if (!self::has(AppContexts::APP_PATH))
        {
            $path = Imi::getNamespacePath($namespace, true);
            self::set(AppContexts::APP_PATH, $path, true);
        }
        if (!self::has(AppContexts::APP_PATH_PHYSICS))
        {
            // @phpstan-ignore-next-line
            self::set(AppContexts::APP_PATH_PHYSICS, IMI_IN_PHAR ? \dirname(realpath($_SERVER['SCRIPT_FILENAME'])) : ($path ?? Imi::getNamespacePath($namespace, true)), true);
        }
        self::$isInited = true;
        Event::trigger('IMI.INITED');
    }

    /**
     * 运行应用.
     *
     * @param string             $vendorParentPath vendor所在目录
     * @param class-string<IApp> $app
     */
    public static function runApp(string $vendorParentPath, string $app, ?callable $callback = null): void
    {
        $fileName = $vendorParentPath . '/imi.cache';
        if (is_file($fileName))
        {
            $preCache = include $fileName;
        }
        else
        {
            $composerJsonFile = $vendorParentPath . '/composer.json';
            if (is_file($composerJsonFile))
            {
                $composerConfig = json_decode(file_get_contents($composerJsonFile), true);
                if (!empty($composerConfig['imi']))
                {
                    $preCache = $composerConfig['imi'];
                    // @phpstan-ignore-next-line
                    if (!IMI_IN_PHAR)
                    {
                        file_put_contents($fileName, '<?php return ' . var_export($preCache, true) . ';');
                    }
                }
            }
        }
        /**
         * imi 框架预缓存.
         */
        \defined('IMI_PRE_CACHE') || \define('IMI_PRE_CACHE', $preCache ?? []);

        if ('cli' !== \PHP_SAPI || false === ($namespace = ImiCommand::getInput()->getParameterOption('--app-namespace', false)))
        {
            if (isset(IMI_PRE_CACHE['namespace']))
            {
                $namespace = IMI_PRE_CACHE['namespace'];
            }
            else
            {
                // @deprecated 3.0
                $appPath = self::get(AppContexts::APP_PATH) ?? $vendorParentPath;
                $config = include $appPath . '/config/config.php';
                if (!isset($config['namespace']))
                {
                    throw new \RuntimeException('imi cannot found your app namespace');
                }
                $namespace = $config['namespace'];
            }
        }

        self::run($namespace, $app, $callback);
    }

    /**
     * 获取应用命名空间.
     */
    public static function getNamespace(): string
    {
        return self::$namespace;
    }

    /**
     * 获取容器对象
     */
    public static function getContainer(): Container
    {
        return self::$container;
    }

    /**
     * 获取Bean对象
     *
     * @template T
     *
     * @param class-string<T> $name
     * @param mixed           ...$params
     *
     * @return T
     */
    public static function getBean(string $name, ...$params)
    {
        return self::$container->get($name, ...$params);
    }

    /**
     * 获取单例对象
     *
     * @param array $params
     */
    public static function getSingleton(string $name, ...$params): object
    {
        return self::$container->getSingleton($name, ...$params);
    }

    /**
     * 当前是否为调试模式.
     */
    public static function isDebug(): bool
    {
        return self::$isDebug;
    }

    /**
     * 开关调试模式.
     */
    public static function setDebug(bool $isDebug): void
    {
        self::$isDebug = $isDebug;
    }

    /**
     * 框架是否已初始化.
     */
    public static function isInited(): bool
    {
        return self::$isInited;
    }

    /**
     * 获取应用上下文数据.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        return self::$context[$name] ?? $default;
    }

    /**
     * 设置应用上下文数据.
     *
     * @param mixed $value
     */
    public static function set(string $name, $value, bool $readonly = false): void
    {
        if (isset(self::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if (!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && str_starts_with($backtrace['class'], 'Imi\\'))
                || (isset($backtrace['function']) && str_starts_with($backtrace['function'], 'Imi\\'))
            ))
            {
                throw new \RuntimeException('Cannot write to read-only application context');
            }
        }
        elseif ($readonly)
        {
            self::$contextReadonly[$name] = true;
        }
        self::$context[$name] = $value;
    }

    /**
     * 设置应用上下文数据，当指定名称不存在时才设置.
     *
     * @param mixed $value
     */
    public static function setNx(string $name, $value, bool $readonly = false): bool
    {
        if (\array_key_exists($name, self::$context))
        {
            return false;
        }
        if (isset(self::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if (!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && str_starts_with($backtrace['class'], 'Imi\\'))
                || (isset($backtrace['function']) && str_starts_with($backtrace['function'], 'Imi\\'))
            ))
            {
                throw new \RuntimeException('Cannot write to read-only application context');
            }
        }
        elseif ($readonly)
        {
            self::$contextReadonly[$name] = true;
        }
        self::$context[$name] = $value;

        return true;
    }

    /**
     * 应用上下文数据是否存在.
     */
    public static function has(string $name): bool
    {
        return \array_key_exists($name, self::$context);
    }

    /**
     * 获取 imi 版本.
     */
    public static function getImiVersion(): string
    {
        if (null !== self::$imiVersion)
        {
            return self::$imiVersion;
        }

        return self::$imiVersion = InstalledVersions::getPrettyVersion('imiphp/imi');
    }

    /**
     * 获取 imi 版本引用 Hash.
     */
    public static function getImiVersionReference(bool $isShort = false): string
    {
        if (null === self::$imiVersionReference)
        {
            self::$imiVersionReference = InstalledVersions::getReference('imiphp/imi') ?? '';
        }

        return $isShort ? substr(self::$imiVersionReference, 0, 7) : self::$imiVersionReference;
    }

    /**
     * 获取 imi 版本号.
     */
    public static function getImiPrettyVersion(): string
    {
        $version = self::getImiVersion();
        $hash = self::getImiVersionReference(true);

        return empty($hash) ? $version : "{$version} ($hash)";
    }

    public static function getAppPharBuildVersion(): ?string
    {
        // @phpstan-ignore-next-line
        if (!IMI_IN_PHAR)
        {
            return '';
        }
        // @phpstan-ignore-next-line
        if (IMI_PHAR_BUILD_GIT_HASH)
        {
            return sprintf(
                 '%s@%s',
                 // @phpstan-ignore-next-line
                 IMI_PHAR_BUILD_GIT_TAG ?? substr(IMI_PHAR_BUILD_GIT_HASH ?? '', 0, 7),
                 IMI_PHAR_BUILD_TIME
             );
        }
        else
        {
            return IMI_PHAR_BUILD_TIME;
        }
    }

    /**
     * Get app 实例对象
     */
    public static function getApp(): IApp
    {
        return self::$app;
    }
}
