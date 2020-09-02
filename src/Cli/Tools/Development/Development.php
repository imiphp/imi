<?php
namespace Imi\Cli\Tools\Development;

use Imi\App;
use Imi\Util\Imi;
use Imi\Util\File;
use Imi\Cli\ArgType;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Contract\BaseCommand;
use Imi\Cli\Annotation\CommandAction;

/**
 * @Command("dev")
 */
class Development extends BaseCommand
{
    /**
     * 生成扩展 IDE 提示帮助
     * 
     * @CommandAction("ext")
     * 
     * @Argument(name="name", type=ArgType::ARRAY, required=true, comments="要生成的扩展名称，支持多个，用半角逗号隔开")
     * @Option(name="path", type=ArgType::STRING, default=null, comments="保存路径")
     * 
     * @return void
     */
    public function ext(array $name, ?string $path): void
    {
        if(null === $path)
        {
            $path = File::path(dirname(Imi::getNamespacePath(App::getNamespace())), 'ide-helper');
        }
        foreach($name as $extName)
        {
            $this->output->writeln('Generating <info>' . $extName . '</info> ...');
            $extensionReflection = new ExtensionReflection($extName);
            $extensionReflection->save(File::path($path, $extName));
        }
        $this->output->writeln('<info>Complete!</info>');
    }

}