<?php

namespace rapidPHP\modules\router\config;

use rapidPHP\modules\common\classier\Build;
use rapidPHP\modules\common\classier\Xml;

class ActionConfig
{

    /**
     * Encode TYPE xml
     */
    const ENCODE_TYPE_XML = 'xml';

    /**
     * Encode TYPE json
     */
    const ENCODE_TYPE_JSON = 'json';

    /**
     * Get Encode TYPE
     * @param $remark
     * @return string|null
     */
    public static function getEncodeType($remark): ?string
    {
        if (empty($remark)) return null;

        if (strtolower(substr($remark, 0, strlen(self::ENCODE_TYPE_XML))) === self::ENCODE_TYPE_XML) {
            return self::ENCODE_TYPE_XML;
        } else if (strtolower(substr($remark, 0, strlen(self::ENCODE_TYPE_JSON))) === self::ENCODE_TYPE_JSON) {
            return self::ENCODE_TYPE_JSON;
        }
        return null;
    }

    /**
     * Get DecodeValue
     * @param $type
     * @param $value
     * @return mixed|null
     */
    public static function getDecodeValue($type, $value)
    {
        switch (strtolower($type)) {
            case self::ENCODE_TYPE_XML:
                if (is_array($value)) return $value;

                return Xml::getInstance()->decode($value);
            case self::ENCODE_TYPE_JSON:
                if (is_array($value)) return $value;

                return Build::getInstance()->jsonDecode($value);
        }
        return $value;
    }
}