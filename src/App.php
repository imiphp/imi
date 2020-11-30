<?php

declare(strict_types=1);

namespace Imi;

use Composer\Autoload\ClassLoader;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Container;
use Imi\Bean\ReflectionContainer;
use Imi\Bean\Scanner;
use Imi\Cache\CacheManager;
use Imi\Core\App\Contract\IApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Main\Helper as MainHelper;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Util\AtomicManager;
use Imi\Util\Composer;
use Imi\Util\Imi;
use Imi\Util\Text;

class App
{
    /**
     * 应用命名空间.
     *
     * @var string
     */
    private static string $namespace;

    /**
     * 容器.
     *
     * @var \Imi\Bean\Container
     */
    private static Container $container;

    /**
     * 框架是否已初始化.
     *
     * @var bool
     */
    private static bool $isInited = false;

    /**
     * 当前是否为调试模式.
     *
     * @var bool
     */
    private static bool $isDebug = false;

    /**
     * Composer ClassLoader.
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private static ?ClassLoader $loader = null;

    /**
     * 运行时数据.
     *
     * @var RuntimeInfo
     */
    private static ?RuntimeInfo $runtimeInfo = null;

    /**
     * 是否协程服务器模式.
     *
     * @var bool
     */
    private static bool $isCoServer = false;

    /**
     * 上下文集合.
     *
     * @var array
     */
    private static array $context = [];

    /**
     * 只读上下文键名列表.
     *
     * @var string[]
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
     *
     * @var \Imi\Core\App\Contract\IApp
     */
    private static IApp $app;

    private function __construct()
    {
    }

    /**
     * 框架服务运行入口.
     *
     * @param string $namespace 应用命名空间
     * @param string $app
     *
     * @return void
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
        Event::trigger('IMI.APP_INIT');
        // 运行
        $appInstance->run();
    }

    /**
     * 框架初始化.
     *
     * @param string $namespace
     *
     * @return void
     */
    public static function initFramework(string $namespace)
    {
        \define('IMI_PATH', __DIR__);
        // 项目命名空间
        static::$namespace = $namespace;
        // 运行时资料，TODO: 移除
        static::$runtimeInfo = new RuntimeInfo();
        // 容器类
        static::$container = new Container();
        // 注解管理器初始化
        AnnotationManager::init();
        static::$isInited = true;
        Event::trigger('IMI.INITED');
    }

    /**
     * 初始化应用.
     *
     * @param bool $noAppCache
     *
     * @return void
     */
    public static function initApp(bool $noAppCache): void
    {
        if ($noAppCache)
        {
            // 扫描组件
            Scanner::scanVendor();
            // 扫描项目
            Scanner::scanApp();

            // 获取配置
            $pools = $caches = [];
            foreach (Helper::getMains() as $main)
            {
                $pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
                $caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
            }
            // 同步池子初始化
            foreach ($pools as $name => $pool)
            {
                if (isset($pool['sync']))
                {
                    $pool = $pool['sync'];
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['class'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
                elseif (isset($pool['pool']['syncClass']))
                {
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['syncClass'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
            }
        }
        else
        {
            while (true)
            {
                exec(Imi::getImiCmd('imi/buildRuntime', [], [
                    'imi-runtime'   => Imi::getRuntimePath('imi-runtime-bak.cache'),
                    'no-app-cache'  => true,
                ]), $output, $code);
                if (0 === $code)
                {
                    break;
                }
                else
                {
                    echo implode(\PHP_EOL, $output), \PHP_EOL;
                }
            }
            Imi::loadRuntimeInfo(Imi::getRuntimePath('runtime.cache'));
            $caches = [];
            foreach (Helper::getMains() as $main)
            {
                $caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
            }
        }
        // 缓存初始化
        foreach ($caches as $name => $cache)
        {
            CacheManager::addName($name, $cache['handlerClass'], $cache['option'] ?? []);
        }
        self::getBean('ErrorLog')->register();
        foreach (Helper::getMains() as $main)
        {
            $config = $main->getConfig();
            // 原子计数初始化
            AtomicManager::setNames($config['atomics'] ?? []);
        }
        AtomicManager::init();
    }

    /**
     * 初始化Main类.
     *
     * @return void
     */
    private static function initMains()
    {
        // 框架
        if (!MainHelper::getMain('Imi', 'Imi'))
        {
            throw new \RuntimeException('Framework imi must have the class Imi\\Main');
        }
        // 项目
        MainHelper::getMain(static::$namespace, 'app');
        Event::trigger('IMI.INIT_MAIN');
    }

    /**
     * 创建服务器对象们.
     *
     * @return void
     */
    public static function createServers()
    {
        // 创建服务器对象们前置操作
        Event::trigger('IMI.SERVERS.CREATE.BEFORE');
        $mainServer = Config::get('@app.mainServer');
        if (null === $mainServer)
        {
            throw new \RuntimeException('config.mainServer not found');
        }
        // 主服务器
        ServerManage::createServer('main', $mainServer);
        // 创建监听子服务器端口
        $subServers = Config::get('@app.subServers', []);
        foreach ($subServers as $name => $config)
        {
            ServerManage::createServer($name, $config, true);
        }
        // 创建服务器对象们后置操作
        Event::trigger('IMI.SERVERS.CREATE.AFTER');
    }

    /**
     * 创建协程服务器.
     *
     * @param string $name
     * @param int    $workerNum
     *
     * @return \Imi\Server\CoServer
     */
    public static function createCoServer($name, $workerNum)
    {
        static::$isCoServer = true;
        $server = ServerManage::createCoServer($name, $workerNum);

        return $server;
    }

    /**
     * 是否协程服务器模式.
     *
     * @return bool
     */
    public static function isCoServer()
    {
        return static::$isCoServer;
    }

    /**
     * 获取应用命名空间.
     *
     * @return string
     */
    public static function getNamespace()
    {
        return static::$namespace;
    }

    /**
     * 获取容器对象
     *
     * @return \Imi\Bean\Container
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * 获取Bean对象
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getBean($name, ...$params)
    {
        return static::$container->get($name, ...$params);
    }

    /**
     * 当前是否为调试模式.
     *
     * @return bool
     */
    public static function isDebug()
    {
        return static::$isDebug;
    }

    /**
     * 开关调试模式.
     *
     * @param bool $isDebug
     *
     * @return void
     */
    public static function setDebug($isDebug)
    {
        static::$isDebug = $isDebug;
    }

    /**
     * 框架是否已初始化.
     *
     * @return bool
     */
    public static function isInited()
    {
        return static::$isInited;
    }

    /**
     * 设置 Composer ClassLoader.
     *
     * @param \Composer\Autoload\ClassLoader $loader
     *
     * @return void
     */
    public static function setLoader(\Composer\Autoload\ClassLoader $loader)
    {
        static::$loader = $loader;
    }

    /**
     * 获取 Composer ClassLoader.
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    public static function getLoader()
    {
        if (null == static::$loader)
        {
            static::$loader = Composer::getClassLoader();
        }

        return static::$loader;
    }

    /**
     * 获取运行时数据.
     *
     * @return RuntimeInfo
     */
    public static function getRuntimeInfo()
    {
        return static::$runtimeInfo;
    }

    /**
     * 获取应用上下文数据.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        return static::$context[$name] ?? $default;
    }

    /**
     * 设置应用上下文数据.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $readonly
     *
     * @return void
     */
    public static function set($name, $value, $readonly = false)
    {
        if (isset(static::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if (!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && Text::startwith($backtrace['class'], 'Imi\\'))
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
     * 获取 imi 版本.
     *
     * @return string
     */
    public static function getImiVersion(): string
    {
        if (null !== static::$imiVersion)
        {
            return static::$imiVersion;
        }
        // composer
        $loader = static::getLoader();
        if ($loader)
        {
            $ref = ReflectionContainer::getClassReflection(\get_class($loader));
            $fileName = \dirname($ref->getFileName(), 3) . '/composer.lock';
            if (is_file($fileName))
            {
                $data = json_decode(file_get_contents($fileName), true);
                foreach ($data['packages'] ?? [] as $item)
                {
                    if ('yurunsoft/imi' === $item['name'])
                    {
                        return static::$imiVersion = $item['version'];
                    }
                }
            }
        }
        // git
        if (false !== strpos(`git --version`, 'git version') && preg_match('/\*([^\r\n]+)/', `git branch`, $matches) > 0)
        {
            return static::$imiVersion = trim($matches[1]);
        }

        return static::$imiVersion = 'Unknown';
    }

    /**
     * Get app 实例对象
     *
     * @return \Imi\Core\Contract\IApp
     */
    public static function getApp(): IApp
    {
        return static::$app;
    }
}
