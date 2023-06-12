<?php

namespace rapidPHP\modules\cache\classier;

use rapidPHP\modules\common\classier\Instances;

abstract class CacheInterface
{

    /**
     * Uses singleton pattern
     */
    use Instances;

    /**
     * Instance does not exist
     * @return static
     */
    public static function onNotInstance()
    {
        return new static(...func_get_args());
    }

    /**
     * CacheInterface constructor.
     * @param mixed ...$options
     */
    abstract public function __construct(...$options);

    /**
     * Check if cache exists
     * @param string $name
     * @return mixed
     */
    abstract public function exists(string $name): bool;

    /**
     * Add or update cache
     * @param string $name Cache name
     * @param $value Cache content
     * @param int $time Expiration time, 0 means no expiration time
     * @return bool
     */
    abstract public function add(string $name, $value, int $time = 0): bool;

    /**
     * Get cache
     * @param string $name Cache name
     * @return array|string|int|mixed|null
     */
    abstract public function get(string $name);

    /**
     * Remove cache
     * @param string $name
     * @return bool
     */
    abstract public function remove(string $name): bool;
}