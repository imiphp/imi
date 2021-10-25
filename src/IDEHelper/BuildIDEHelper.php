<?php

declare(strict_types=1);

namespace Imi\IDEHelper;

use function file_put_contents;
use Imi\Bean\BeanManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Util\Imi;

class BuildIDEHelper implements IEventListener
{
    protected string $beanMapping = '';

    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $this->buildBean();
        $this->save();
        Log::info('buildIdeHelper');
    }

    protected function buildBean(): void
    {
        $mappingStr = "        '' => '@',\n";

        foreach (BeanManager::getMap() as $name => $item)
        {
            if (!isset($item['class']) || $name !== $item['class']['beanName'])
            {
                continue;
            }
            $item = $item['class'];
            ['beanName' => $beanName, 'className' => $className] = $item;
            $mappingStr .= "        '{$beanName}' => {$className}::class,\n";
        }

        $this->beanMapping = $mappingStr;
    }

    protected function save(): void
    {
        $output = <<<META
        <?php

        namespace PHPSTORM_META {
            override(\Imi\App::getBean(0), map([
        {$this->beanMapping}
            ]));

            override(\Imi\RequestContext::getBean(0), map([
        {$this->beanMapping}
            ]));

            override(\Imi\Server\Contract\IServer::getBean(0), map([
        {$this->beanMapping}
            ]));
        }
        META;

        $metaFile = Imi::getRuntimePath('.phpstorm.meta.php');
        file_put_contents($metaFile, $output);
    }
}
