<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: grpc.proto

namespace Grpc;

use UnexpectedValueException;

/**
 * Protobuf type <code>grpc.Test</code>
 */
class Test
{
    /**
     * Generated from protobuf enum <code>A = 0;</code>
     */
    const A = 0;
    /**
     * Generated from protobuf enum <code>B = 2;</code>
     */
    const B = 2;

    private static $valueToName = [
        self::A => 'A',
        self::B => 'B',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}
