<?php


namespace rapidPHP\modules\application\config;


use Exception;
use rapidPHP\modules\common\classier\Modules;

class ApplicationConfig
{
    /**
     * Module name
     */
    const MODULE_NAME = 'application';

    /**
     * Default configuration file name
     */
    const DEFAULT_CONFIG_FILENAME = 'default.config.php';

    /**
     * Get app default configuration
     * @return array
     * @throws Exception
     */
    public static function getDefaultConfig(): array
    {
        $file = Modules::getInstance()->getModulesResourcePath(self::MODULE_NAME) . self::DEFAULT_CONFIG_FILENAME;

        if (!is_file($file)) throw new Exception('config file error!');

        return require $file . '';
    }
}