<?php
namespace rapidPHP;

use rapidPHP\modules\core\classier\DI;
use Exception;
use rapidPHP\modules\common\classier\AB;
use rapidPHP\modules\common\classier\AR;
use rapidPHP\modules\common\classier\Build;
use rapidPHP\modules\common\classier\Calendar;
use rapidPHP\modules\common\classier\File;
use rapidPHP\modules\common\classier\Register;
use rapidPHP\modules\common\classier\StrCharacter;
use rapidPHP\modules\common\classier\Verify;
use rapidPHP\modules\common\classier\Xml;
use rapidPHP\modules\core\classier\web\ViewTemplate;
use rapidPHP\modules\reflection\classier\Classify;

// Check PHP environment
if (version_compare(PHP_VERSION, '7.2.0', '<')) die('require PHP > 7.2.0 !');

// Running mode
define('RAPIDPHP_VERSION', '4.0.2');

// Running mode
define('APP_RUNNING_SAPI_NAME', php_sapi_name());

// Whether running mode is command line
define('APP_RUNNING_IS_SHELL', preg_match("/cli/i", php_sapi_name()) ? true : false);

// Project root directory
define('PATH_ROOT', str_replace('\\', '/', dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/');

// rapidPHP framework root directory
define('PATH_RAPIDPHP_ROOT', str_replace('\\', '/', dirname(dirname(__DIR__))) . '/src/');

// Current framework root directory
define('PATH_FRAMEWORK', PATH_RAPIDPHP_ROOT . 'rapidPHP/');

// Modules directory
define('PATH_MODULES', PATH_FRAMEWORK . 'modules/');

// Current running file root directory
define('PATH_RUNTIME', PATH_ROOT . 'runtime/');

// Current app running file directory
if (defined('PATH_APP')) define('PATH_APP_RUNTIME', PATH_APP . 'runtime/');

if (!defined('SWOOLE_HOOK_ALL')) define('SWOOLE_HOOK_ALL', 1879048191);
if (!defined('SWOOLE_HOOK_CURL')) define('SWOOLE_HOOK_CURL', 268435456);

/**
 * Quickly get the ArrayObject class
 * @param $array
 * @return AB
 */
function AB($array = null): AB
{
    return AB::getInstance($array);
}

/**
 * Quickly get the Array class
 * @return AR
 */
function AR(): AR
{
    return AR::getInstance();
}

/**
 * Quickly get the build class
 * @return Build
 */
function B(): Build
{
    return Build::getInstance();
}

/**
 * Quickly get the StrCharacter class
 * @return StrCharacter
 */
function Str(): StrCharacter
{
    return StrCharacter::getInstance();
}

/**
 * Quickly get the Calendar class
 * @return Calendar
 */
function Cal(): Calendar
{
    return Calendar::getInstance();
}

/**
 * Get the file operation class
 * @return File
 */
function F(): File
{
    return File::getInstance();
}

/**
 * Get the verification operation class
 * @return Verify
 */
function V(): Verify
{
    return Verify::getInstance();
}

/**
 * Get the xml operation class
 * @return Xml
 */
function X(): Xml
{
    return Xml::getInstance();
}

/**
 * Instantiates a class hub. If the class has already been instantiated, it is automatically retrieved from before.
 * @param $name string The name of the class. If using namespaces, the namespace must be included.
 * @param array $parameter
 * @param bool $forced
 * @return array|null|object|string
 * @throws Exception
 */
function M(string $name, array $parameter = [], bool $forced = false)
{
    if ($forced == false) {
        if (Register::getInstance()->isPut($name)) {
            return Register::getInstance()->get($name);
        } else {
            $object = Classify::getInstance($name)->newInstanceArgs($parameter);
            Register::getInstance()->put($name, $object);
            return $object;
        }
    } else {
        $object = Classify::getInstance($name)->newInstanceArgs($parameter);
        Register::getInstance()->put($name, $object);
        return $object;
    }
}

/**
 * Get the ViewTemplate object of the current view
 * @param $view
 * @return ViewTemplate
 */
function VT($view): ?ViewTemplate
{
    if ($view instanceof ViewTemplate) return $view;
    return null;
}

/**
 * DI
 * @template T
 * @param $class
 * @param ...$supports
 * @return T|false|void
 */
function DI($class, ...$supports)
{
    if (func_num_args() <= 1) {
        if (!is_array($class)) {
            return DI::resolveArgument($class);
        } else {
            DI::supportsParameter($class);
        }
    } else {
        DI::supportParameter($class, ...$supports);
    }
    return;
}

/**
 * Format Exception
 * @param Exception $e
 * @param string $format
 * @return string
 */
function formatException(Exception $e, string $format = "{msg} {code}\n{trace}\n thrown in {file} on line {line}"): string
{
    $result = [
        'code' => $e->getCode(),
        'msg' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
    foreach ($result as $key => $value) {
        $format = str_replace("{{$key}}", $value, $format);
    }
    return $format;
}