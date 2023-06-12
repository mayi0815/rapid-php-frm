<?php

namespace rapidPHP\modules\cache\classier;

use Exception;
use rapidPHP\modules\common\classier\Build;
use rapidPHP\modules\common\classier\File;

class FileCache extends CacheInterface
{

    /**
     * Cache path
     * @var string
     */
    protected $cachePath;

    /**
     * FileCache constructor.
     * @param mixed ...$options
     * @throws Exception
     */
    public function __construct(...$options)
    {
        $this->cachePath = $options[0] ?? null;

        if (empty($this->cachePath)) throw new Exception('cache path error!');

        if (!is_dir($this->cachePath) && !mkdir($this->cachePath, 0777, true))
            throw new Exception('创建缓存目录失败!');
    }

    /**
     * FileCache constructor.: FileCache constructor.
     * @param $name
     * @return string
     */
    private function getCacheName($name): string
    {
        return $this->cachePath . md5($name);
    }

    /**
     * exists
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        $cacheFile = $this->getCacheName($name);

        return is_file($cacheFile);
    }

    /**
     * Add cache
     * @param string $name cache name
     * @param $value -值
     * @param int $time time limit 0- no time limit
     * @return bool
     * @throws Exception
     */
    public function add(string $name, $value, int $time = 0): bool
    {
        $cache = ['data' => $value];

        if (is_int($time) && $time > 0) $cache['time'] = time() + $time;

        return File::getInstance()->write($this->getCacheName($name), serialize($cache));
    }

    /**
     * Get Cache
     * @param string $name
     * @return array|string|int|mixed|null
     */
    public function get(string $name)
    {
        $cacheFile = $this->getCacheName($name);

        if (!is_file($cacheFile)) return null;

        $cache = unserialize(file_get_contents($cacheFile));

        if (empty($cache)) return null;

        $time = array_key_exists('time', $cache) ? $cache['time'] : null;

        $data = Build::getInstance()->getData($cache, 'data');

        if (!is_int($time)) return $data;

        if (time() <= $time) {
            return $data;
        } else {
            $this->remove($name);

            return null;
        }
    }

    /**
     * Remove Cache
     * @param string $name
     * @return bool
     */
    public function remove(string $name): bool
    {
        $cacheFile = $this->getCacheName($name);

        if (!is_file($cacheFile)) return true;

        return unlink($cacheFile);
    }
}
