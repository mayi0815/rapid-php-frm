<?php

namespace rapidPHP\modules\common\classier;

class AB
{
    /**
     * Gel Model
     */
    const MODEL_GET = 1;

    /**
     * Delete Model
     */
    const MODEL_DEL = 2;

    /**
     * @var array
     */
    protected $data;

    /**
     * AB constructor.
     * @param $data
     */
    public function __construct($data = null)
    {
        $this->data($data);
    }

    /**
     * toInstance
     * @param array|AB|null $data
     * @return AB
     */
    public static function getInstance($data = null)
    {
        return new static($data);
    }

    /**
     * Set data
     * @param $data
     */
    public function data($data = null)
    {
        if ($data === null) $data = [];

        $this->data = $data;
    }

    /**
     * Get Data
     * @param array|null $names
     * @param int $mode 1获取names下的values 2,排除names下的values
     * @return array
     */
    public function toData(?array $names = null, int $mode = self::MODEL_GET): array
    {
        if (!empty($names)) {
            switch ($mode) {
                case self::MODEL_GET:
                    return AR::getInstance()->getArray($this->data, $names);
                case self::MODEL_DEL:
                    return AR::getInstance()->delete($this->data, $names);
            }
        }

        return $this->data;
    }


    /**
     * If is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Verify if the name parameter exists
     * @param $name
     * @return bool
     */
    public function hasName($name): bool
    {
        if (empty($this->data)) return false;

        return array_key_exists($name, $this->data);
    }

    /**
     * Get Value
     * @param $name
     * @return mixed
     */
    public function toValue($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Compare Value
     * @param $name
     * @param $value
     * @return bool
     */
    public function hasValue($name, $value): bool
    {
        return $this->toValue($name) == $value;
    }

    /**
     * Set value
     * @param $name
     * @param $value
     */
    public function value($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get child element as arrayObject objec
     * @param $name
     * @return static
     */
    public function toAB($name)
    {
        $array = array();

        if (isset($this->data[$name])) $array = $this->data[$name];

        $clone = clone $this;

        $clone->data($array);

        return $clone;
    }

    /**
     * Get Array
     * @param $name
     * @return array
     */
    public function toArray($name): array
    {
        return (array)$this->toValue($name);
    }

    /**
     * Get String
     * @param $name
     * @return string
     */
    public function toString($name): string
    {
        return (string)$this->toValue($name);
    }

    /**
     * Get int
     * @param $name
     * @return int
     */
    public function toInt($name): int
    {
        return (int)$this->toValue($name);
    }

    /**
     * Get bool
     * @param $name
     * @return bool
     */
    public function toBool($name): bool
    {
        return (bool)$this->toValue($name);
    }

    /**
     * Get float
     * @param $name
     * @return float
     */
    public function toFloat($name): float
    {
        return (float)$this->toValue($name);
    }

    /**
     * Get object
     * @param $name
     * @return object
     */
    public function toObject($name)
    {
        return (object)$this->toValue($name);
    }

    /**
     * toLength
     * @return int
     */
    public function toLength(): int
    {
        return count($this->data);
    }

    /**
     * Convert to Xml
     * @param array|null $names
     * @return string
     */
    public function toXml(?array $names = null): string
    {
        return Xml::getInstance()->encode($this->toData($names));
    }

    /**
     * Convert to json
     * @param array|null $names
     * @return string
     */
    public function toJson(?array $names = null): string
    {
        return json_encode($this->toData($names));
    }

    /**
     * Delete specified value
     * @param $names
     */
    public function delValue($names)
    {
        if (is_array($names)) {
            foreach ($names as $key) {
                unset($this->data[$key]);
            }
        } else {
            unset($this->data[$names]);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
