<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator;

use Imi\Model\BaseModel;
use Imi\Model\IdGenerator\Contract\IIdGenerator;

class UUIDGenerator implements IIdGenerator
{
    /**
     * @return mixed
     */
    public function generate(?BaseModel $model, array $options = [])
    {
        switch ($type = $options['type'] ?? UUIDGeneratorType::RANDOM)
        {
            case UUIDGeneratorType::TIME:
                return uuid_create(UUID_TYPE_TIME);
            case UUIDGeneratorType::RANDOM:
                return uuid_create(UUID_TYPE_RANDOM);
            case UUIDGeneratorType::MD5:
            case UUIDGeneratorType::SHA1:
                $functionName = 'uuid_generate_' . $type;
                if (isset($options['ns']))
                {
                    $ns = $options['ns'];
                }
                elseif (!isset($options['nsField']) || null === ($ns = $model[$options['nsField']]))
                {
                    throw new \InvalidArgumentException('The ns or nsField option in the uuid is required.');
                }

                if (isset($options['name']))
                {
                    $name = $options['name'];
                }
                elseif (isset($options['nameField']))
                {
                    $name = $model[$options['nameField']] ?? 'imi';
                }
                else
                {
                    throw new \InvalidArgumentException('The name or nameField option in the uuid is required.');
                }

                return $functionName($ns, $name);
            default:
                throw new \InvalidArgumentException(sprintf('Invalid value %s in enum %s', $type, static::class));
        }
    }
}
