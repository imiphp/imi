<?php
namespace Imi\Test\Inject\Classes;

use Imi\Config;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\ConstValue;
use Imi\Config\Annotation\ConfigValue;

/**
 * @Bean("TestInjectValue")
 */
class TestInjectValue
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

    public function test()
    {
        Assert::assertEquals(Config::get('@app.imi'), $this->imi);
        Assert::assertEquals(PHP_VERSION, $this->phpVersion);
    }
}