<?php
namespace Imi\Test\Inject\Classes;

use Imi\Config;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Bean\Annotation\Callback;
use Imi\Aop\Annotation\ConstValue;
use Imi\Aop\Annotation\CallableValue;
use Imi\Config\Annotation\ConfigValue;

/**
 * @Bean("TestInjectValueLogic")
 */
class TestInjectValueLogic
{
    /**
     * @ConfigValue("@app.imi")
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
        Assert::assertEquals(Config::get('@app.imi'), $this->imi);
        Assert::assertEquals(PHP_VERSION, $this->phpVersion);
        Assert::assertEquals(['A', 'test'], $this->callable);
        Assert::assertEquals(phpversion(), $this->callableResult);
    }
}