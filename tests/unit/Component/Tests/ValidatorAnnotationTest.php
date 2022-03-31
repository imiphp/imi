<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Bean\BeanFactory;
use Imi\Test\BaseTest;
use Imi\Test\Component\Enum\TestEnum;
use Imi\Test\Component\Validate\Classes\TestAutoConstructValidator;
use Imi\Test\Component\Validate\Classes\TestSceneAnnotationValidator;
use Imi\Test\Component\Validate\Classes\TestSceneValidator;
use Imi\Test\Component\Validate\Classes\TestValidator;

/**
 * @testdox Validator Annotation
 */
class ValidatorAnnotationTest extends BaseTest
{
    /**
     * @var \Imi\Test\Component\Validate\Classes\TestValidator
     */
    private $tester;

    private array $data = [];

    public function testValidatorAnnotation(): void
    {
        $this->tester = new TestValidator($this->data);
        $this->success();
        $this->compareFail();
        $this->decimalFail();
        $this->enumFail();
        $this->inFail();
        $this->intFail();
        $this->requiredFail();
        $this->numberFail();
        $this->textFail();
        $this->textCharFail();
        $this->validateValueFail();
        $this->regexFail();
        $this->optional();
        $this->star();
    }

    public function testAutoConstructValidator(): void
    {
        $this->initData();
        $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);

        // int fail
        $this->data['int'] = 1000;
        try
        {
            $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);
            $this->assertTrue(false, 'Construct validate property fail');
        }
        catch (\Throwable $th)
        {
            $this->assertStringEndsWith('1000 不符合大于等于0且小于等于100', $th->getMessage());
        }

        try
        {
            $test = new TestAutoConstructValidator();
            $this->assertTrue(false, 'Construct validate fail');
        }
        catch (\Throwable $th)
        {
        }
    }

    public function testMethodAutoValidate(): void
    {
        $this->initData();
        $test = BeanFactory::newInstance(TestAutoConstructValidator::class, $this->data);
        $this->assertEquals(1, $test->test(1));
        try
        {
            $test->test(-1);
            $this->assertTrue(false, 'Method validate fail');
        }
        catch (\Throwable $th)
        {
        }
    }

    private function initData(): void
    {
        $this->data = [
            'compare'       => -1,
            'decimal'       => 1.25,
            'enum'          => TestEnum::A,
            'in'            => 1,
            'int'           => 1,
            'required'      => '',
            'number'        => 1,
            'text'          => 'imiphp.com',
            'chars'         => 'imiphp.com',
            'validateValue' => -1,
            'optional'      => 1,
            'regex'         => 123,
            'list1'         => [
                ['id' => 1, 'name' => 'test1'],
                ['id' => 2, 'name' => 'test2'],
            ],
            'list2'         => [1, 2, 3],
        ];
    }

    private function success(): void
    {
        $this->initData();
        $result = $this->tester->validate();
        $this->assertTrue($result, $this->tester->getMessage() ?: '');
    }

    private function compareFail(): void
    {
        $this->initData();
        $this->data['compare'] = 1;
        $this->assertFalse($this->tester->validate());
    }

    private function decimalFail(): void
    {
        $this->initData();
        $this->data['decimal'] = 1.222;
        $this->assertFalse($this->tester->validate());

        $this->data['decimal'] = 0;
        $this->assertFalse($this->tester->validate());

        $this->data['decimal'] = 11;
        $this->assertFalse($this->tester->validate());
    }

    private function enumFail(): void
    {
        $this->initData();
        $this->data['enum'] = 100;
        $this->assertFalse($this->tester->validate());
    }

    private function inFail(): void
    {
        $this->initData();
        $this->data['in'] = 100;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('100 不在列表内', $this->tester->getMessage());
    }

    private function intFail(): void
    {
        $this->initData();
        $this->data['int'] = -1;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('-1 不符合大于等于0且小于等于100', $this->tester->getMessage());

        $this->data['int'] = 'a';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('a 不符合大于等于0且小于等于100', $this->tester->getMessage());
    }

    private function requiredFail(): void
    {
        $this->initData();
        unset($this->data['required']);
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('required为必须参数', $this->tester->getMessage());
    }

    private function numberFail(): void
    {
        $this->initData();
        $this->data['number'] = 1.234;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('数值必须大于等于0.01，小于等于999.99，小数点最多保留2位小数，当前值为1.234', $this->tester->getMessage());

        $this->data['number'] = 0;
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('数值必须大于等于0.01，小于等于999.99，小数点最多保留2位小数，当前值为0', $this->tester->getMessage());
    }

    private function textFail(): void
    {
        $this->initData();
        $this->data['text'] = '';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('text参数长度必须>=6 && <=12', $this->tester->getMessage());

        $this->data['text'] = '1234567890123';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('text参数长度必须>=6 && <=12', $this->tester->getMessage());
    }

    private function textCharFail(): void
    {
        $this->initData();

        $this->data['chars'] = '这个可以通过';
        $this->assertTrue($this->tester->validate());

        $this->data['chars'] = '测试不通过';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('chars参数长度必须>=6 && <=12', $this->tester->getMessage());

        $this->data['chars'] = '测试不通过测试不通过测试不通过';
        $this->assertFalse($this->tester->validate());
        $this->assertEquals('chars参数长度必须>=6 && <=12', $this->tester->getMessage());
    }

    private function validateValueFail(): void
    {
        $this->initData();
        $this->data['validateValue'] = '1';
        $this->assertFalse($this->tester->validate());
    }

    private function regexFail(): void
    {
        $this->initData();
        $this->data['regex'] = 'a1';
        $this->assertFalse($this->tester->validate());
    }

    private function optional(): void
    {
        $this->initData();
        $this->data['optional'] = -1;
        $this->assertFalse($this->tester->validate());

        unset($this->data['optional']);
        $this->assertTrue($this->tester->validate());
    }

    private function star(): void
    {
        $this->initData();
        $this->data['list1'][0]['id'] = 11;
        $this->assertFalse($this->tester->validate());

        $this->initData();
        $this->data['list2'][0] = 11;
        $this->assertFalse($this->tester->validate());
    }

    public function testScene(): void
    {
        $data = [
            'decimal' => 1.1,
        ];
        $validator = new TestSceneValidator($data);
        $this->assertTrue($validator->setCurrentScene('a')->validate());

        $data = [
            'decimal' => 'a',
        ];
        $validator = new TestSceneValidator($data);
        $this->assertFalse($validator->setCurrentScene('a')->validate());

        $data = [
            'int' => 1,
        ];
        $validator = new TestSceneValidator($data);
        $this->assertTrue($validator->setCurrentScene('b')->validate());

        $data = [
            'int' => 'b',
        ];
        $validator = new TestSceneValidator($data);
        $this->assertFalse($validator->setCurrentScene('b')->validate());

        $data = [
            'decimal' => 1.1,
            'int'     => 1,
        ];
        $validator = new TestSceneValidator($data);
        $this->assertTrue($validator->setCurrentScene('b')->validate());

        $data = [
            'decimal' => 'a',
            'int'     => 'b',
        ];
        $validator = new TestSceneValidator($data);
        $this->assertFalse($validator->setCurrentScene('b')->validate());

        // 全部
        $data = [
            'decimal' => 1.1,
            'int'     => 1,
        ];
        $validator = new TestSceneValidator($data);
        $this->assertTrue($validator->validate());

        $data = [
            'decimal' => 'a',
            'int'     => 'b',
        ];
        $validator = new TestSceneValidator($data);
        $this->assertFalse($validator->validate());
    }

    public function testSceneWithAnnotation(): void
    {
        $data = [
            'decimal' => 1.1,
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertTrue($validator->setCurrentScene('a')->validate());

        $data = [
            'decimal' => 'a',
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertFalse($validator->setCurrentScene('a')->validate());

        $data = [
            'int' => 1,
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertTrue($validator->setCurrentScene('b')->validate());

        $data = [
            'int' => 'b',
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertFalse($validator->setCurrentScene('b')->validate());

        $data = [
            'decimal' => 1.1,
            'int'     => 1,
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertTrue($validator->setCurrentScene('b')->validate());

        $data = [
            'decimal' => 'a',
            'int'     => 'b',
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertFalse($validator->setCurrentScene('b')->validate());

        // 全部
        $data = [
            'decimal' => 1.1,
            'int'     => 1,
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertTrue($validator->validate());

        $data = [
            'decimal' => 'a',
            'int'     => 'b',
        ];
        $validator = new TestSceneAnnotationValidator($data);
        $this->assertFalse($validator->validate());
    }
}
