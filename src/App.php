<?php

declare(strict_types=1);

namespace Imi;

use Composer\InstalledVersions;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Container;
use Imi\Bean\ReflectionContainer;
use Imi\Bean\Scanner;
use Imi\Core\App\Contract\IApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Util\Composer;
use Imi\Util\Imi;
use Imi\Util\Text;
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
     *
     * @var string
     */
    private static ?string $imiVersion = null;

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
     * @param string $namespace 应用命名空间
     */
    public static function run(string $namespace, string $app): void
    {
        /** @var \Imi\Core\App\Contract\IApp $appInstance */
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
        self::getBean('ErrorLog')->register();
        Event::trigger('IMI.APP_RUN');
        // 运行
        $appInstance->run();
    }

    /**
     * 框架初始化.
     */
    public static function initFramework(string $namespace): void
    {
        \define('IMI_PATH', __DIR__);
        // 项目命名空间
        static::$namespace = $namespace;
        // 容器类
        static::$container = new Container();
        // 注解管理器初始化
        AnnotationManager::init();
        if (!self::has(AppContexts::APP_PATH))
        {
            self::set(AppContexts::APP_PATH, Imi::getNamespacePath($namespace), true);
        }
        static::$isInited = true;
        Event::trigger('IMI.INITED');
    }

    /**
     * 获取应用命名空间.
     */
    public static function getNamespace(): string
    {
        return static::$namespace;
    }

    /**
     * 获取容器对象
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * 获取Bean对象
     *
     * @param array $params
     */
    public static function getBean(string $name, ...$params): object
    {
        return static::$container->get($name, ...$params);
    }

    /**
     * 获取单例对象
     *
     * @param array $params
     */
    public static function getSingleton(string $name, ...$params): object
    {
        return static::$container->getSingleton($name, ...$params);
    }

    /**
     * 当前是否为调试模式.
     */
    public static function isDebug(): bool
    {
        return static::$isDebug;
    }

    /**
     * 开关调试模式.
     */
    public static function setDebug(bool $isDebug): void
    {
        static::$isDebug = $isDebug;
    }

    /**
     * 框架是否已初始化.
     */
    public static function isInited(): bool
    {
        return static::$isInited;
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
        return static::$context[$name] ?? $default;
    }

    /**
     * 设置应用上下文数据.
     *
     * @param mixed $value
     */
    public static function set(string $name, $value, bool $readonly = false): void
    {
        if (isset(static::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if (!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && Text::startwith($backtrace['class'], 'Imi\\'))
                || (isset($backtrace['function']) && Text::startwith($backtrace['function'], 'Imi\\'))
            ))
            {
                throw new \RuntimeException('Cannot write to read-only application context');
            }
        }
        elseif ($readonly)
        {
            static::$contextReadonly[$name] = true;
        }
        static::$context[$name] = $value;
    }

    /**
     * 设置应用上下文数据，当指定名称不存在时才设置.
     *
     * @param mixed $value
     */
    public static function setNx(string $name, $value, bool $readonly = false): bool
    {
        if (\array_key_exists($name, static::$context))
        {
            return false;
        }
        if (isset(static::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if (!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && Text::startwith($backtrace['class'], 'Imi\\'))
                || (isset($backtrace['function']) && Text::startwith($backtrace['function'], 'Imi\\'))
            ))
            {
                throw new \RuntimeException('Cannot write to read-only application context');
            }
        }
        elseif ($readonly)
        {
            static::$contextReadonly[$name] = true;
        }
        static::$context[$name] = $value;

        return true;
    }

    /**
     * 应用上下文数据是否存在.
     */
    public static function has(string $name): bool
    {
        return \array_key_exists($name, static::$context);
    }

    /**
     * 获取 imi 版本.
     */
    public static function getImiVersion(): string
    {
        if (null !== static::$imiVersion)
        {
            return static::$imiVersion;
        }
        $version = InstalledVersions::getPrettyVersion('imiphp/imi');
        $hash = InstalledVersions::getReference('imiphp/imi');
        $hash = substr($hash, 0, 7);
        return static::$imiVersion = "{$version} ({$hash})";
    }

    /**
     * Get app 实例对象
     */
    public static function getApp(): IApp
    {
        return static::$app;
    }
}
