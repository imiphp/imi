<?php
namespace Imi;

use Imi\Config;
use Imi\Util\Imi;
use Imi\Util\Args;
use Imi\Util\Text;
use Imi\Event\Event;
use Imi\Util\Composer;
use Imi\Bean\Container;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Config\Dotenv\Dotenv;
use Imi\Util\Process\ProcessType;
use Imi\Main\Helper as MainHelper;
use Imi\Util\CoroutineChannelManager;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\ReflectionContainer;

abstract class App
{
    /**
     * 应用命名空间
     * @var string
     */
    private static $namespace;

    /**
     * 容器
     * @var \Imi\Bean\Container
     */
    private static $container;

    /**
     * 框架是否已初始化
     * @var boolean
     */
    private static $isInited = false;

    /**
     * 当前是否为调试模式
     * @var boolean
     */
    private static $isDebug = false;

    /**
     * Composer ClassLoader
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private static $loader;

    /**
     * 运行时数据
     *
     * @var RuntimeInfo
     */
    private static $runtimeInfo;

    /**
     * 是否协程服务器模式
     *
     * @var boolean
     */
    private static $isCoServer = false;

    /**
     * 上下文集合
     *
     * @var array
     */
    private static $context = [];

    /**
     * 只读上下文键名列表
     *
     * @var string[]
     */
    private static $contextReadonly = [];

    /**
     * imi 版本号，来源于 composer.lock
     *
     * @var string
     */
    private static $imiVersion;

    /**
     * 框架服务运行入口
     * @param string $namespace 应用命名空间
     * @return void
     */
    public static function run($namespace)
    {
        static::checkEnvironment();
        self::set(ProcessAppContexts::PROCESS_NAME, ProcessType::MASTER, true);
        self::set(ProcessAppContexts::MASTER_PID, getmypid(), true);
        self::set(ProcessAppContexts::SCRIPT_NAME, realpath($_SERVER['SCRIPT_FILENAME']));
        static::initFramework($namespace);
        if(!isset($_SERVER['argv'][1]))
        {
            echo "Has no operation! You can try the command: \033[33;33m", $_SERVER['argv'][0], " server/start\033[0m", PHP_EOL;
            return;
        }
        Event::trigger('IMI.INITED');
    }

    /**
     * 检查环境
     *
     * @return void
     */
    private static function checkEnvironment()
    {
        // Swoole 检查
        if(!extension_loaded('swoole'))
        {
            echo 'No Swoole extension installed or enabled', PHP_EOL;
            exit;
        }
        // 短名称检查
        $useShortname = ini_get_all('swoole')['swoole.use_shortname']['local_value'];
        $useShortname = strtolower(trim(str_replace('0', '', $useShortname)));
        if (in_array($useShortname, ['', 'off', 'false'], true))
        {
            echo 'Please enable swoole short name before using imi!', PHP_EOL, 'You can set swoole.use_shortname = on into your php.ini.', PHP_EOL;
            exit;
        }
    }

    /**
     * 框架初始化
     * 
     * @param string $namespace
     * @return void
     */
    public static function initFramework(string $namespace)
    {
        static::$namespace = $namespace;
        $isServerStart = ('server/start' === ($_SERVER['argv'][1] ?? null));
        if($isServerStart)
        {
            self::outImi();
            self::outStartupInfo();
        }
        AnnotationManager::init();
        static::$runtimeInfo = new RuntimeInfo;
        static::$container = new Container;
        // .env
        $dotenv = new Dotenv(Imi::getNamespacePaths(static::$namespace));
        $dotenv->init();
        // 初始化Main类
        static::initMains();
        // 运行时目录写权限检测
        if(!is_writable($runtimePath = Imi::getRuntimePath()))
        {
            echo 'Runtime path "', $runtimePath, '" is not writable', PHP_EOL;
            exit;
        }
        // 框架运行时缓存支持
        if($isServerStart)
        {
            $result = false;
        }
        else if($file = Args::get('imi-runtime'))
        {
            // 尝试加载指定 runtime
            $result = App::loadRuntimeInfo($file);
        }
        else
        {
            // 尝试加载默认 runtime
            $result = App::loadRuntimeInfo(Imi::getRuntimePath('imi-runtime.cache'));
        }
        if(!$result)
        {
            // 不使用缓存时去扫描
            Annotation::getInstance()->init([
                MainHelper::getMain('Imi', 'Imi'),
            ]);
            if($isServerStart)
            {
                Imi::buildRuntime(Imi::getRuntimePath('imi-runtime-bak.cache'));
            }
        }
        static::$isInited = true;
    }

    /**
     * 初始化Main类
     * @return void
     */
    private static function initMains()
    {
        // 框架
        if(!MainHelper::getMain('Imi', 'Imi'))
        {
            throw new \RuntimeException('Framework imi must have the class Imi\\Main');
        }
        // 项目
        if(!MainHelper::getMain(static::$namespace, 'app'))
        {
            throw new \RuntimeException(sprintf('Your app must have the class %s\\Main', static::$namespace));
        }
        // 服务器们
        $servers = array_merge(['main'=>Config::get('@app.mainServer')], Config::get('@app.subServers', []));
        foreach($servers as $serverName => $item)
        {
            if($item && !MainHelper::getMain($item['namespace'], 'server.' . $serverName))
            {
                throw new \RuntimeException(sprintf('Server [%s] must have the class %s\\Main', $serverName, $item['namespace']));
            }
        }
    }

    /**
     * 创建服务器对象们
     * @return void
     */
    public static function createServers()
    {
        // 创建服务器对象们前置操作
        Event::trigger('IMI.SERVERS.CREATE.BEFORE');
        $mainServer = Config::get('@app.mainServer');
        if(null === $mainServer)
        {
            throw new \RuntimeException('config.mainServer not found');
        }
        // 主服务器
        ServerManage::createServer('main', $mainServer);
        // 创建监听子服务器端口
        $subServers = Config::get('@app.subServers', []);
        foreach($subServers as $name => $config)
        {
            ServerManage::createServer($name, $config, true);
        }
        // 创建服务器对象们后置操作
        Event::trigger('IMI.SERVERS.CREATE.AFTER');
    }

    /**
     * 创建协程服务器
     *
     * @param string $name
     * @param int $workerNum
     * @return \Imi\Server\CoServer
     */
    public static function createCoServer($name, $workerNum)
    {
        static::$isCoServer = true;
        $server = ServerManage::createCoServer($name, $workerNum);
        return $server;
    }

    /**
     * 是否协程服务器模式
     *
     * @return boolean
     */
    public static function isCoServer()
    {
        return static::$isCoServer;
    }

    /**
     * 获取应用命名空间
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
     * @param string $name
     * @return mixed
     */
    public static function getBean($name, ...$params)
    {
        return static::$container->get($name, ...$params);
    }

    /**
     * 当前是否为调试模式
     * @return boolean
     */
    public static function isDebug()
    {
        return static::$isDebug;
    }

    /**
     * 开关调试模式
     * @param boolean $isDebug
     * @return void
     */
    public static function setDebug($isDebug)
    {
        static::$isDebug = $isDebug;
    }

    /**
     * 框架是否已初始化
     * @return boolean
     */
    public static function isInited()
    {
        return static::$isInited;
    }

    /**
     * 初始化 Worker，但不一定是 Worker 进程
     *
     * @return void
     */
    public static function initWorker()
    {
        App::loadRuntimeInfo(Imi::getRuntimePath('runtime.cache'), true);

        // Worker 进程初始化前置
        Event::trigger('IMI.INIT.WORKER.BEFORE');

        $appMains = MainHelper::getAppMains();
        
        // 日志初始化
        if(static::$container->has('Logger'))
        {
            $logger = static::getBean('Logger');
            foreach($appMains as $main)
            {
                foreach($main->getConfig()['beans']['Logger']['exHandlers'] ?? [] as $exHandler)
                {
                    $logger->addExHandler($exHandler);
                }
            }
        }

        // 初始化
        PoolManager::clearPools();
        if(Coroutine::isIn())
        {
            $pools = Config::get('@app.pools', []);
            foreach($appMains as $main)
            {
                // 协程通道队列初始化
                CoroutineChannelManager::setNames($main->getConfig()['coroutineChannels'] ?? []);
        
                // 异步池子初始化
                $pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
            }
            foreach($pools as $name => $pool)
            {
                if(isset($pool['async']))
                {
                    $pool = $pool['async'];
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['class'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
                else if(isset($pool['pool']['asyncClass']))
                {
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['asyncClass'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
            }
        }
        else
        {
            $pools = Config::get('@app.pools', []);
            foreach($appMains as $main)
            {
                // 同步池子初始化
                $pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
            }
            foreach($pools as $name => $pool)
            {
                if(isset($pool['sync']))
                {
                    $pool = $pool['sync'];
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['class'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
                else if(isset($pool['pool']['syncClass']))
                {
                    $poolPool = $pool['pool'];
                    PoolManager::addName($name, $poolPool['syncClass'], new PoolConfig($poolPool['config']), $pool['resource']);
                }
            }
        }

        // 缓存初始化
        CacheManager::clearPools();
        $caches = Config::get('@app.caches', []);
        foreach($appMains as $main)
        {
            $caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
        }
        foreach($caches as $name => $cache)
        {
            CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
        }

        // Worker 进程初始化后置
        Event::trigger('IMI.INIT.WORKER.AFTER');
    }

    /**
     * 设置 Composer ClassLoader
     *
     * @param \Composer\Autoload\ClassLoader $loader
     * @return void
     */
    public static function setLoader(\Composer\Autoload\ClassLoader $loader)
    {
        static::$loader = $loader;
    }

    /**
     * 获取 Composer ClassLoader
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    public static function getLoader()
    {
        if(null == static::$loader)
        {
            static::$loader = Composer::getClassLoader();
        }
        return static::$loader;
    }

    /**
     * 获取运行时数据
     *
     * @return RuntimeInfo
     */
    public static function getRuntimeInfo()
    {
        return static::$runtimeInfo;
    }

    /**
     * 从文件加载运行时数据
     * $minimumAvailable 设为 true，则 getRuntimeInfo() 无法获取到数据
     *
     * @param string $fileName
     * @param bool $minimumAvailable
     * @return boolean
     */
    public static function loadRuntimeInfo($fileName, $minimumAvailable = false)
    {
        if(!is_file($fileName))
        {
            return false;
        }
        // Swoole 4.4.x 下 hook file_get_contents 有奇怪 bug，所以根据不同情况用不同方法
        if(Coroutine::isIn())
        {
            $content = Coroutine::readFile($fileName);
        }
        else
        {
            $content = file_get_contents($fileName);
        }
        static::$runtimeInfo = unserialize($content);
        if(!$minimumAvailable)
        {
            Annotation::getInstance()->getParser()->loadStoreData(static::$runtimeInfo->annotationParserData);
            Annotation::getInstance()->getParser()->setParsers(static::$runtimeInfo->annotationParserParsers);
        }
        AnnotationManager::setAnnotations(static::$runtimeInfo->annotationManagerAnnotations);
        AnnotationManager::setAnnotationRelation(static::$runtimeInfo->annotationManagerAnnotationRelation);
        foreach(static::$runtimeInfo->parsersData as $parserClass => $data)
        {
            $parser = $parserClass::getInstance();
            $parser->setData($data);
        }
        Event::trigger('IMI.LOAD_RUNTIME_INFO');
        if($minimumAvailable)
        {
            static::$runtimeInfo = null;
        }
        return true;
    }

    /**
     * 输出 imi 图标
     *
     * @return void
     */
    public static function outImi()
    {
        echo <<<STR
 _               _ 
(_)  _ __ ___   (_)
| | | '_ ` _ \  | |
| | | | | | | | | |
|_| |_| |_| |_| |_|


STR;
    }

    /**
     * 输出启动信息
     *
     * @return void
     */
    public static function outStartupInfo()
    {
        echo '[System]', PHP_EOL;
        $system = (defined('PHP_OS_FAMILY') && 'Unknown' !== PHP_OS_FAMILY) ? PHP_OS_FAMILY : PHP_OS;
        switch($system)
        {
            case 'Linux':
                $system .= ' - ' . Imi::getLinuxVersion();
                break;
            case 'Darwin':
                $system .= ' - ' . Imi::getDarwinVersion();
                break;
            case 'CYGWIN':
                $system .= ' - ' . Imi::getCygwinVersion();
                break;
        }
        echo 'System: ', $system, PHP_EOL;
        if(Imi::isDockerEnvironment())
        {
            echo 'Virtual machine: Docker', PHP_EOL;
        }
        else if(Imi::isWSL())
        {
            echo 'Virtual machine: WSL', PHP_EOL;
        }
        echo 'CPU: ', swoole_cpu_num(), ' Cores', PHP_EOL;
        echo 'Disk: Free ', round(@disk_free_space('.') / (1024*1024*1024), 3), ' GB / Total ', round(@disk_total_space('.') / (1024*1024*1024), 3), ' GB', PHP_EOL;

        echo PHP_EOL, '[Network]', PHP_EOL;
        foreach(swoole_get_local_ip() as $name => $ip)
        {
            echo 'ip@', $name, ': ', $ip, PHP_EOL;
        }

        echo PHP_EOL, '[PHP]', PHP_EOL;
        echo 'Version: v', PHP_VERSION, PHP_EOL;
        echo 'Swoole: v', SWOOLE_VERSION, PHP_EOL;
        echo 'imi: ', static::getImiVersion(), PHP_EOL;
        echo 'Timezone: ', date_default_timezone_get(), PHP_EOL;

        echo PHP_EOL;
    }

    /**
     * 获取应用上下文数据
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        return static::$context[$name] ?? $default;
    }

    /**
     * 设置应用上下文数据
     * @param string $name
     * @param mixed $value
     * @param bool $readonly
     * @return void
     */
    public static function set($name, $value, $readonly = false)
    {
        if(isset(static::$contextReadonly[$name]))
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $backtrace = $backtrace[1] ?? null;
            if(!(
                (isset($backtrace['object']) && $backtrace['object'] instanceof \Imi\Bean\IBean)
                || (isset($backtrace['class']) && Text::startwith($backtrace['class'], 'Imi\\'))
            ))
            {
                throw new \RuntimeException('Cannot write to read-only application context');
            }
        }
        else if($readonly)
        {
            static::$contextReadonly[$name] = true;
        }
        static::$context[$name] = $value;
    }

    /**
     * 获取 imi 版本
     *
     * @return string
     */
    public static function getImiVersion(): string
    {
        if(null !== static::$imiVersion)
        {
            return static::$imiVersion;
        }
        $loader = static::getLoader();
        if($loader)
        {
            $ref = ReflectionContainer::getClassReflection(get_class($loader));
            $fileName = dirname($ref->getFileName(), 3) . '/composer.lock';
            if(is_file($fileName))
            {
                $data = json_decode(file_get_contents($fileName), true);
                foreach($data['packages'] ?? [] as $item)
                {
                    if('yurunsoft/imi' === $item['name'])
                    {
                        return static::$imiVersion = $item['version'];
                    }
                }
            }
        }
        return static::$imiVersion = 'Unknown';
    }

}