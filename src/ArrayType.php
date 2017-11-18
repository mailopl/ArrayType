<?php

namespace Spacegrass\ArrayType;

class ArrayType
{
    const STRING  = 'string';
    const INTEGER = 'integer';
    const FLOAT   = 'double';
    const DOUBLE  = 'double';
    const BOOLEAN = 'boolean';

    const ARRAY   = 'array';

    const SCALAR_TYPES = [
        self::STRING,
        self::INTEGER,
        self::DOUBLE,
        self::BOOLEAN,
    ];

    /**
     * Enforces an array to contain items of a particular type
     *
     * @param array $items
     * @param string $type
     * @return array
     */
    public static function enforce(array $items, string $type): array
    {
        return array_map(function ($item) use ($type) {
            if (!self::passes($item, $type)) {
                $itemType = is_scalar($item) ? gettype($item) : get_class($item);
                throw new \TypeError('Array item of type ' . $itemType . ' must be instance of: ' . $type);
            }
            return $item;
        }, $items);
    }

    /**
     * @param mixed $item
     * @param string $type
     * @return bool
     */
    private static function passes($item, string $type): bool
    {
        if (self::requiresScalar($type) || is_array($item)) {
            return gettype($item) == $type;
        }

        return is_a($item, $type);
    }

    /**
     * @param string $type
     * @return bool
     */
    private static function requiresScalar(string $type): bool
    {
        return in_array($type, self::SCALAR_TYPES);
    }
}

