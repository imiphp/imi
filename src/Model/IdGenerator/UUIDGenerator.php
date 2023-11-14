<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator;

use Imi\Model\BaseModel;
use Imi\Model\IdGenerator\Contract\IIdGenerator;

class UUIDGenerator implements IIdGenerator
{
    /**
     * @param array{type?: UUIDGeneratorType, ns?: string, nsField?: string, name?: string, nameField?: string} $options
     */
    public function generate(?BaseModel $model, array $options = []): mixed
    {
        switch ($type = $options['type'] ?? UUIDGeneratorType::Random)
        {
            case UUIDGeneratorType::Time:
                return uuid_create(UUID_TYPE_TIME);
            case UUIDGeneratorType::Random:
                return uuid_create(UUID_TYPE_RANDOM);
            case UUIDGeneratorType::MD5:
            case UUIDGeneratorType::SHA1:
                $functionName = 'uuid_generate_' . $type->name;
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
                throw new \InvalidArgumentException(sprintf('Invalid value %s in enum %s', $type->name, static::class));
        }
    }
}
