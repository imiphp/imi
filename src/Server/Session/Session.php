<?php

declare(strict_types=1);

namespace Imi\Server\Session;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade(class="SessionManager", request=true, args={})
 *
 * @method static void start(?string $sessionId = NULL)
 * @method static void close()
 * @method static void destroy()
 * @method static void save()
 * @method static void commit()
 * @method static bool isStart()
 * @method static string getName()
 * @method static string getId()
 * @method static \Imi\Server\Session\Handler\ISessionHandler getHandler()
 * @method static void tryGC()
 * @method static void gc()
 * @method static mixed get(?string $name = NULL, $default = NULL)
 * @method static void set(string $name, $value)
 * @method static void delete(string $name)
 * @method static mixed once(string $name, $default = NULL)
 * @method static void clear()
 * @method static \Imi\Server\Session\SessionConfig getConfig()
 * @method static string parseName(string $name)
 * @method static bool isChanged()
 * @method static bool isNewSession()
 */
class Session extends BaseFacade
{
}
