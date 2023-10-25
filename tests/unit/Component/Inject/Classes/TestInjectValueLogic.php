<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\CallableValue;
use Imi\Aop\Annotation\ConstValue;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Callback;
use Imi\Config;
use Imi\Config\Annotation\ConfigValue;
use Imi\Config\Annotation\EnvValue;
use PHPUnit\Framework\Assert;

#[Bean(name: 'TestInjectValueLogicXXX')]
class TestInjectValueLogic
{
    /**
     * @var string
     */
    #[ConfigValue(name: '@app.imi-framework')]
    protected $imi;

    /**
     * @var string
     */
    #[ConstValue(name: 'PHP_VERSION')]
    protected $phpVersion;

    /**
     * @var callable
     */
    #[Callback(class: 'A', method: 'test')]
    protected $callable;

    /**
     * @var callable
     */
    #[CallableValue(callable: 'phpversion')]
    protected $callableResult;

    /**
     * @var mixed
     */
    #[EnvValue(name: 'yurun')]
    protected $yurun;

    public function test(): void
    {
        Assert::assertEquals(Config::get('@app.imi-framework'), $this->imi);
        Assert::assertEquals(\PHP_VERSION, $this->phpVersion);
        Assert::assertEquals(['A', 'test'], $this->callable);
        Assert::assertEquals(\PHP_VERSION, $this->callableResult);
        Assert::assertEquals(777, $this->yurun);
    }
}
