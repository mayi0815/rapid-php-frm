<?php

namespace rapidPHP\modules\common\classier;

class AR
{
    /**
     * Use singleton mode
     */
    use Instances;

    /**
     * Instance does not exist
     * @return static
     */
    public static function onNotInstance()
    {
        return new static();
    }

    /**
     * Get the value inside the array value
     * Support a.b.c.e
     * @param $array array
     * @param $name
     * @return array|mixed|null
     */
    public function value(array $array, $name)
    {
        if (empty($array)) return null;

        if (empty($name)) return null;

        $names = explode('.', $name);

        while (!empty($names)) {
            $key = array_shift($names);

            if (!array_key_exists($key, $array)) return null;

            $array = $array[$key];
        }

        return $array;
    }

    /**
     * Batch delete array elements
     * @param array|null $array $array
     * array(
     *  'a'=>1,'b'=>2,'c'=>3
     * )
     * @param $key
     * array(
     *      'a','c'
     * )
     * @param bool|false $sort
     * @param bool $isTow
     * @return array
     * array(
     *      'b'=>2
     * )
     */
    public function delete(?array $array, $key, bool $sort = false, bool $isTow = false): ?array
    {
        $keys = !is_array($key) ? explode(',', $key) : $key;

        if ($isTow) {
            foreach ($keys as $keyName) {
                foreach ($array as $index => $value) {
                    unset($array[$index][$keyName]);
                }
            }
        } else {
            foreach ($keys as $keyName) unset($array[$keyName]);
        }

        return $sort ? self::sort($array) : $array;
    }

    /**
     * Batch get the value in the array
     * @param array|null $array $array
     * array(
     *      'a'=>1,'b'=>2,'c'=>3
     * )
     * @param array $key
     * array(
     *      'a','c'
     * )
     * @param bool $isSort
     * @return array
     * array('a'=>1,'c'=>3)
     */
    public function getArray(?array $array, array $key, bool $isSort = false): array
    {
        $newArray = [];

        if ($isSort === false) {

            foreach ($key as $value) $newArray[$value] = Build::getInstance()->getData($array, $value);

        } else {

            foreach ($key as $value) $newArray[] = Build::getInstance()->getData($array, $value);
        }
        return $newArray;
    }

    /**
     * Rename
     * @param array $array
     * @param array $key
     * @return array
     */
    public function rename(array $array, array $key): array
    {
        foreach ($array as $name => $value) {
            unset($array[$name]);
            $array[Build::getInstance()->getData($key, $name)] = $value;
        }

        return $array;
    }


    /**
     * Get the value of the specified key in the array
     * @param array $array
     * @param $key
     * @return mixed|null
     */
    public function getArrayFirstValue(array $array, $key = null): ?array
    {
        if (is_array($key)) {
            $result = [];

            foreach ($array as $value) {

                foreach ($key as $keyName) {
                    $result[$keyName] = Build::getInstance()->getData($value, $keyName);
                }
            }

            return $result;
        } else if (empty($key)) {
            foreach ($array as $value) return $value;
        } else {
            foreach ($array as $value) return Build::getInstance()->getData($value, $key);
        }

        return null;
    }

    /**
     * Determine if the array contains the specified value
     * @param array $array
     * array(
     *  1,2,3
     * )
     * @param $value => 3
     * @param $key
     * @return bool => true
     */
    public function isAppointValue(array $array, $value, $key = null): bool
    {
        if (is_null($key)) {
            foreach ($array as $v) {

                if ($v == $value) return true;
            }
        } else {
            foreach ($array as $v) {

                if ($v[$key] == $value) return true;
            }
        }
        return false;
    }


    /**
     * Determine if all array values are equal to the specified value
     * @param array $array
     * array(
     *      'e','e','e'
     * )
     * @param $value => e
     * @param $key
     * @return bool => true
     */
    public function isAllAppointValue(array $array, $value, $key = null): bool
    {
        if (is_null($key)) {
            foreach ($array as $v) {

                if ($value !== $v) return false;
            }
        } else {
            foreach ($array as $v) {

                if ($value !== $v[$key]) return false;
            }
        }
        return true;
    }


    /**
     * Get the depth of a multidimensional array
     * @param array $array
     * @return int
     */
    public function getDepth(array $array): int
    {
        $maxDepth = 1;

        foreach ($array as $value) {

            if (is_array($value)) {

                $depth = self::getDepth($value) + 1;

                if ($depth > $maxDepth) $maxDepth = $depth;
            }
        }
        return $maxDepth;
    }


    /**
     * Reindexing a multidimensional array.
     * @param array $array
     * @return array
     */
    public function sort(array $array): array
    {
        $newArray = [];

        foreach ($array as $val) {

            if (is_array($val)) {

                $newArray[] = self::sort($val);
            } else {
                $newArray[] = $val;
            }
        }
        return $newArray;
    }

    /**
     * Merge multidimensional array.
     * @param array $array
     * array(
     *      array(1),array(2)
     * )
     * @return array
     * array(1,2)
     */
    public function mergeArrayValues(array $array): array
    {
        $newArray = [];

        foreach ($array as $value) {
            if (is_array($value)) {

                $newArray = array_merge($newArray, self::mergeArrayValues($value));
            } else {

                $newArray[] = $value;
            }
        }
        return $newArray;
    }

    /**
     * Deep merging arrays.
     * @param $source
     * @param $overlap
     */
    public function merge(&$source, $overlap)
    {
        if (!is_array($source)) $source = (array)$source;

        if (!is_array($overlap)) $overlap = (array)$overlap;

        foreach ($overlap as $item => $value) {
            if (isset($source[$item]) && is_array($source[$item])) {
                $this->merge($source[$item], $value);
            } else {
                $source[$item] = $value;
            }
        }
    }


    /**
     * Get duplicate array for the specified key
     * @param array $array
     * array(
     *      array('key'=>'value'),
     *      array('key'=>'value'),
     *      array('key'=>'value'),
     *      array('key'=>'value1'),
     * )
     * @param $key =>'key'
     * @param $of
     * @param bool $isOne
     * @return array
     * array(
     *      'value'=>array(
     *          array('key'=>'value'),
     *          array('key'=>'value'),
     *          array('key'=>'value'),
     *      )
     *      'value1'=>array(
     *           array('key'=>'value1')
     *      )
     * )
     */
    public function arrayColumn(array $array, $key, $of = null, bool $isOne = false): array
    {
        $newArray = [];

        if (is_null($of)) {
            foreach ($array as $value) {

                $index = Build::getInstance()->getData($value, $key);

                if ($isOne) {
                    $newArray[$index] = $value;
                } else {
                    $newArray[$index][] = $value;

                }
            }
        } else {
            return array_column($array, $key, $of);
        }

        return $newArray;
    }

    /**
     * Randomly select a few elements from the array
     * @param array $array
     * @param int $number
     * @param bool $isRepeat
     * @return array
     */
    public function rands(array $array, int $number = 1, bool $isRepeat = false): array
    {
        $rand = [];

        if (is_array($array) && $array) {

            while (count($rand) < $number) {

                $mt_rand = $array[mt_rand(0, count($array) - 1)];

                if ($isRepeat === true) {
                    $rand[] = $mt_rand;
                } else if ($isRepeat === false) {

                    if (array_search($mt_rand, $rand) === false) {
                        $rand[] = $mt_rand;

                    } else if (count($rand) == count($array) && $number > count($array)) {
                        break;
                    }
                }
            }
        }
        return $rand;
    }

    /**
     * Flip the key-value pairs in the array
     * array(
     *  a=>1
     * )
     * @param $array
     * @param bool $isValue
     * @return array
     * array(
     *  1=>a|null
     * )
     */
    public function valueToKey($array, bool $isValue = true): array
    {
        $newArray = [];

        if (is_array($array)) {

            foreach ($array as $item => $value) if (!is_array($value)) $newArray[$value] = $isValue ? $item : null;
        }
        return $newArray;
    }

    /**
     * Remove duplicate values from the array
     * @param $array
     * @return array
     */
    public function removalOfDuplicationValue($array): array
    {
        $result = [];

        foreach ($array as $value) if (!isset($result[$value])) $result[$value] = $value;

        shuffle($result);

        return $result;
    }

    /**
     * Get tree-structured data
     * @param $array
     * @param string $pKey
     * @param string $idKey
     * @param string $childKey
     * @param int|null $pid
     * @return array
     */
    public function getTree($array, string $pKey = 'pid', string $idKey = 'id', string $childKey = 'child', int $pid = null): array
    {
        $tree = [];

        foreach ($array as $value) {
            if ($value[$pKey] === $pid) {

                $value[$childKey] = $this->getTree($array, $pKey, $idKey, $childKey, $value[$idKey]);

                if (empty($value[$childKey])) unset($value[$childKey]);

                $tree[] = $value;
            }
        }

        return $tree;
    }
}
