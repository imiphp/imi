<?php

declare(strict_types=1);

namespace Imi\Grpc\Util;

use Google\Protobuf\EnumValueDescriptor;
use Google\Protobuf\Internal\Descriptor;
use Google\Protobuf\Internal\DescriptorPool;
use Google\Protobuf\Internal\EnumDescriptor;
use Google\Protobuf\Internal\FieldDescriptor;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\Message;
use Imi\Bean\ReflectionContainer;
use Imi\Util\Text;

class ProtobufUtil
{
    /**
     * 向 Grpc Message 对象设置值，每次设置前会清空所有的值
     *
     * @param array|object $data
     */
    public static function setMessageData(Message $message, $data, bool $ignoreUnknown = false): void
    {
        $message->clear();
        if (\is_object($data))
        {
            $message->mergeFromJsonString(json_encode($data), $ignoreUnknown);
        }
        else
        {
            $ref = ReflectionContainer::getMethodReflection(\get_class($message), 'mergeFromJsonArray');
            $ref->setAccessible(true);
            $ref->invoke($message, $data, $ignoreUnknown);
        }
    }

    /**
     * 实例化并初始化一个消息.
     *
     * @param array|object $data
     */
    public static function newMessage(string $messageClass, $data): Message
    {
        $message = new $messageClass();
        self::setMessageData($message, $data, true);

        return $message;
    }

    /**
     * 实例化并初始化一个消息列表.
     *
     * @return Message[]
     */
    public static function newMessageArray(string $messageClass, array $dataList): array
    {
        $list = [];
        foreach ($dataList as $data)
        {
            $list[] = self::newMessage($messageClass, $data);
        }

        return $list;
    }

    /**
     * 获取 Grpc Message 对象本身代表的值
     *
     * @param Message|\Traversable $message
     *
     * @return mixed
     */
    public static function getMessageValue($message, array $options = [])
    {
        if ($message instanceof \Google\Protobuf\Any)
        {
            $value = $message->unpack();
            if ($value instanceof Message)
            {
                $value = self::getMessageValue($value, $options);
                $value['@type'] = $message->getTypeUrl();
            }

            return $value;
        }
        if ($message instanceof \Google\Protobuf\BoolValue
            || $message instanceof \Google\Protobuf\BytesValue
            || $message instanceof \Google\Protobuf\DoubleValue
            || $message instanceof \Google\Protobuf\FloatValue
            || $message instanceof \Google\Protobuf\Int32Value
            || $message instanceof \Google\Protobuf\Int64Value
            || $message instanceof \Google\Protobuf\StringValue
            || $message instanceof \Google\Protobuf\UInt32Value
            || $message instanceof \Google\Protobuf\UInt64Value
        ) {
            return $message->getValue();
        }
        if ($message instanceof \Google\Protobuf\Duration)
        {
            return GPBUtil::formatDuration($message) . 's';
        }
        if ($message instanceof \Google\Protobuf\EnumValue)
        {
            switch ($enumReturnType = ($options['enumReturnType'] ?? 'value'))
            {
                case 'value':
                    return $message->getNumber();
                case 'name':
                    return $message->getName();
                default:
                    throw new \RuntimeException(sprintf('Unknown enumReturnType %s', $enumReturnType));
            }
        }
        if ($message instanceof \Google\Protobuf\GPBEmpty)
        {
            return [];
        }
        if ($message instanceof \Google\Protobuf\Struct)
        {
            $value = [];
            foreach ($message->getFields() as $key => $field)
            {
                $value[$key] = self::getMessageValue($field, $options);
            }

            return $value;
        }
        if ($message instanceof \Google\Protobuf\Value)
        {
            $method = 'get' . Text::toPascalName($message->getKind());
            $value = $message->{$method}();
            if ($value instanceof Message)
            {
                return self::getMessageValue($value, $options);
            }
            else
            {
                return $value;
            }
        }
        if ($message instanceof \Google\Protobuf\ListValue)
        {
            $value = [];
            foreach ($message->getValues() as $valueItem)
            {
                $value[] = self::getMessageValue($valueItem, $options);
            }

            return $value;
        }
        if ($message instanceof \Google\Protobuf\FieldMask)
        {
            return GPBUtil::formatFieldMask($message);
        }
        if ($message instanceof \Google\Protobuf\Timestamp)
        {
            return GPBUtil::formatTimestamp($message);
        }
        if ($message instanceof \Traversable)
        {
            $result = [];
            foreach ($message as $key => $value)
            {
                $result[$key] = self::getMessageValue($value, $options);
            }

            return $result;
        }
        if ($message instanceof Message)
        {
            /** @var DescriptorPool $pool */
            $pool = DescriptorPool::getGeneratedPool();
            /** @var Descriptor $desc */
            $desc = $pool->getDescriptorByClassName(\get_class($message));
            $result = [];
            /** @var FieldDescriptor $field */
            foreach ($desc->getField() as $field)
            {
                $methodName = $field->getGetter();
                $value = $message->{$methodName}();
                $result[$field->getJsonName()] = self::parseFieldValue($field, $value, $options);
            }

            return $result;
        }

        return $message;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function parseFieldValue(FieldDescriptor $field, $value, array $options = [])
    {
        if (null === $value)
        {
            return null;
        }
        if ($field->isMap())
        {
            $map = $value;
            $value = [];
            /** @var Descriptor $messageType */
            $messageType = $field->getMessageType();
            /** @var FieldDescriptor $valueField */
            $valueField = $messageType->getFieldByNumber(2);
            foreach ($map as $mapKey => $mapValue)
            {
                $value[$mapKey] = self::parseFieldValue($valueField, $mapValue, $options);
            }

            return $value;
        }
        elseif ($field->isRepeated())
        {
            $map = $value;
            $value = [];
            /** @var Descriptor|null $messageType */
            $messageType = $field->getMessageType();
            foreach ($map as $mapValue)
            {
                if ($messageType)
                {
                    $value[] = self::getMessageValue($mapValue, $options);
                }
                else
                {
                    $value[] = $mapValue;
                }
            }

            return $value;
        }
        else
        {
            switch ($field->getType())
            {
                case GPBType::ENUM:
                    switch ($enumReturnType = ($options['enumReturnType'] ?? 'value'))
                    {
                        case 'value':
                            return $value;
                        case 'name':
                            /** @var EnumDescriptor $enumType */
                            $enumType = $field->getEnumType();
                            /** @var EnumValueDescriptor $valueType */
                            $valueType = $enumType->getValueByNumber($value);

                            return $valueType->getName();
                        default:
                            throw new \RuntimeException(sprintf('Unknown enumReturnType %s', $enumReturnType));
                    }
                    // no break
                case GPBType::MESSAGE:
                    return self::getMessageValue($value, $options);
                default:
                    return $value;
            }
        }
    }
}
