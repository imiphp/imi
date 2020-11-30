<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\CallableValue;
use Imi\Aop\Annotation\ConstValue;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Callback;
use Imi\Config;
use Imi\Config\Annotation\ConfigValue;
use PHPUnit\Framework\Assert;

/**
 * @Bean("TestInjectValueLogic")
 */
class TestInjectValueLogic
{
    /**
     * @ConfigValue("@app.imi-framework")
     *
     * @var string
     */
    protected $imi;

    /**
     * @ConstValue("PHP_VERSION")
     *
     * @var string
     */
    protected $phpVersion;

    /**
     * @Callback(class="A", method="test")
     *
     * @var callable
     */
    protected $callable;

    /**
     * @CallableValue("phpversion")
     *
     * @var callable
     */
    protected $callableResult;

    public function test()
    {
        Assert::assertEquals(Config::get('@app.imi-framework'), $this->imi);
        Assert::assertEquals(\PHP_VERSION, $this->phpVersion);
        Assert::assertEquals(['A', 'test'], $this->callable);
        Assert::assertEquals(PHP_VERSION, $this->callableResult);
    }
}
