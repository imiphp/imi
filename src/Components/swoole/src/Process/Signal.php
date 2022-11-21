<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\Swoole\Util\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine\System;

class Signal
{
    /**
     * @var Channel[][]
     */
    private static array $waitingSignals = [];

    private static array $cids = [];

    /**
     * @var callable[][]
     */
    private static array $waitCallbacks = [];

    private static array $waitingProcessSignals = [];

    private function __construct()
    {
    }

    public static function wait(int $signo, float $timeout = -1): bool
    {
        $channel = new Channel(1);
        $needCreateCoroutineWait = !isset(self::$waitingSignals[$signo]);
        $coid = Coroutine::getCid();
        self::$waitingSignals[$signo][$coid] = $channel;
        if ($needCreateCoroutineWait)
        {
            self::$cids[$signo] = Coroutine::create(static function () use ($signo) {
                $waitResult = System::waitSignal($signo);
                if (!isset(self::$waitingSignals[$signo]))
                {
                    return;
                }
                $channels = self::$waitingSignals[$signo];
                unset(self::$waitingSignals[$signo]);
                foreach ($channels as $channel)
                {
                    $channel->push($waitResult);
                }
                unset(self::$cids[$signo]);
            });
        }

        return $channel->pop($timeout);
    }

    public static function waitCallback(int $signo, callable $callback): void
    {
        self::$waitCallbacks[$signo][] = $callback;
        if (!isset(self::$waitingProcessSignals[$signo]))
        {
            self::$waitingProcessSignals[$signo] = true;
            Process::signal($signo, static function ($signo) {
                if (!isset(self::$waitCallbacks[$signo]))
                {
                    return;
                }
                $callbacks = self::$waitCallbacks[$signo];
                unset(self::$waitCallbacks[$signo]);
                foreach ($callbacks as $callback)
                {
                    $callback($signo);
                }
                unset(self::$waitingProcessSignals[$signo]);
            });
        }
    }

    public static function clear(): void
    {
        $waitingSignals = self::$waitingSignals;
        self::$waitCallbacks = self::$waitingProcessSignals = self::$waitingSignals = [];
        foreach ($waitingSignals as $channels)
        {
            foreach ($channels as $channel)
            {
                $channel->close();
            }
        }
        foreach (self::$cids as $cid)
        {
            Coroutine::cancel($cid);
        }
        self::$cids = [];
    }
}
