<?php

namespace Imi\Tool\Tools\Development;

use Imi\App;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @Tool("dev")
 */
class Development
{
    /**
     * 生成扩展 IDE 提示帮助.
     *
     * @Operation("ext")
     *
     * @Arg(name="name", type=ArgType::ARRAY, required=true, comments="要生成的扩展名称，支持多个，用半角逗号隔开")
     * @Arg(name="path", type=ArgType::STRING, default=null, comments="保存路径")
     *
     * @return void
     */
    public function ext($name, $path)
    {
        if (null === $path)
        {
            $path = File::path(\dirname(Imi::getNamespacePath(App::getNamespace())), 'ide-helper');
        }
        foreach ($name as $extName)
        {
            echo 'Generating ', $extName, '...', \PHP_EOL;
            $extensionReflection = new ExtensionReflection($extName);
            $extensionReflection->save(File::path($path, $extName));
        }
        echo 'Complete!', \PHP_EOL;
    }
}
