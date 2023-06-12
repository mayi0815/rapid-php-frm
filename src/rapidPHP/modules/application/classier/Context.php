<?php


namespace rapidPHP\modules\application\classier;


use rapidPHP\modules\router\classier\Action;
use rapidPHP\modules\router\classier\Interceptor;
use rapidPHP\modules\router\classier\Route;
use rapidPHP\modules\router\classier\Router;

abstract class Context
{

    /**
     * decode
     */
    const OPTIONS_DECODE = 1;

    /**
     * Decode the real path
     */
    const OPTIONS_DECODE_REALPATH = self::OPTIONS_DECODE << 1;

    /**
     * Decode get request parameters
     */
    const OPTIONS_DECODE_REQUEST_GET = self::OPTIONS_DECODE << 2;

    /**
     * Decode post request parameters
     */
    const OPTIONS_DECODE_REQUEST_POST = self::OPTIONS_DECODE << 3;

    /**
     * Decode cookie request parameters
     */
    const OPTIONS_DECODE_REQUEST_COOKIE = self::OPTIONS_DECODE << 6;

    /**
     * @var array
     */
    protected $supports;

    /**
     * Interceptor
     * @var Interceptor[]
     */
    protected $interceptors = [];

    /**
     * @var int
     */
    protected $decodeOptions = self::OPTIONS_DECODE;


    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->supports = [
            Context::class => $this,
        ];
    }


    /**
     * @return int
     */
    public function getDecodeOptions(): int
    {
        return $this->decodeOptions;
    }

    /**
     * @param int $decodeOptions
     */
    public function setDecodeOptions(int $decodeOptions): void
    {
        $this->decodeOptions = $decodeOptions;
    }

    /**
     * @return array
     */
    public function getInterceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * add Interceptor
     * @param Interceptor $interceptors
     */
    public function addInterceptor(Interceptor $interceptors): void
    {
        $this->interceptors[] = $interceptors;
    }

    /**
     * Before route matching
     * @param Router $router
     */
    public function onMatchingBefore(Router $router)
    {

    }

    /**
     * Before calling the action method
     * @param Router $router
     * @param Action $action
     * @param Route $route
     * @param $pathVariable
     * @param $realPath
     */
    public function onInvokeActionBefore(Router $router, Action $action, Route $route, $pathVariable, $realPath)
    {
        foreach ($this->interceptors as $interceptor) {
            if ($interceptor->isInExclude($realPath)) {
                continue;
            } else if ($interceptor->isInRole($realPath, $role)) {
                $interceptor->onHandler($router, $action, $route, $pathVariable, $realPath, $role);
            }
        }
    }


    /**
     * After calling the action method
     * @param Router $router
     * @param Action $action
     * @param Route $route
     * @param $result
     */
    public function onInvokeActionAfter(Router $router, Action $action, Route $route, &$result)
    {

    }

    /**
     * Define parameters
     * @param mixed ...$merge
     */
    public function supportsParameter(...$merge)
    {
        if (empty($merge)) return;

        foreach ($merge as $value) {
            $this->supports = array_merge($this->supports, $value);
        }
    }

    /**
     * Reverse
     * @param $class
     * @param mixed ...$supports
     * @return string|null|false|object
     * If returned false, class may not be found. If null, reverse call may be empty or errored.
     */
    public function resolveArgument($class, ...$supports)
    {
        if (empty($class)) return false;

        $supports = array_merge($this->supports, ...$supports);

        $class = ltrim($class, '\\');

        foreach ($supports as $support => $getter) {
            if ($support === $class) {
                return !is_callable($getter) ? $getter : call_user_func($getter);
            } else if (is_subclass_of($support, $class)) {
                return !is_callable($getter) ? $getter : call_user_func($getter);
            }
        }

        return false;
    }

    /**
     * Unified decoding
     * @param $value
     */
    public function decode(&$value)
    {

    }
}