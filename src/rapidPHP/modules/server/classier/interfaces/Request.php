<?php
namespace rapidPHP\modules\server\classier\interfaces;

use Exception;
use rapidPHP\modules\common\classier\Build;
use rapidPHP\modules\common\classier\Uri;
use rapidPHP\modules\common\classier\XSS;
use rapidPHP\modules\core\classier\Model;
use rapidPHP\modules\io\classier\Input;
use rapidPHP\modules\server\config\SessionConfig;
use rapidPHP\modules\server\config\HttpConfig;

abstract class Request implements Input
{
    /**
     * Http request GET parameters, equivalent to $_GET in PHP, formatted as an array
     * To prevent HASH attacks, GET parameters are not allowed to exceed 128
     * @var array
     */
    public $get;

    /**
     * The size of POST and Header cannot exceed the setting of package_max_length, otherwise it will be considered a malicious request
     * The maximum number of POST parameters does not exceed 128
     * @var array HTTP POST parameters, formatted as an array
     */
    public $post;

    /**
     * files
     * @var array
     */
    public $files;

    /**
     * COOKIE information carried by HTTP request, same as $_COOKIE in PHP, formatted as an array.
     * @var array
     */
    public $cookie;

    /**
     * Header information of HTTP request. The type is an array, and the first letter of all keys is capitalized, and they are connected by -.
     * @var array
     */
    public $header;

    /**
     * HTTP request-related server information, equivalent to the $_SERVER array in PHP. Contains information such as the method, URL path, and client IP of the HTTP request. All keys are capitalized
     * @var array
     */
    public $serverInfo;

    /**
     * @var SessionConfig|null
     */
    public $sessionConfig;

    /**
     * sessionId
     * @var string|null
     */
    public $sessionId;

    /**
     * Get the renamed server, all keys are converted to uppercase
     * @param $server
     * @return array
     */
    public static function getRenameServerInfo($server): array
    {
        return array_change_key_case(is_null($server) ? [] : $server, CASE_UPPER);
    }

    /**
     * Request constructor.
     * @param $get
     * @param $post
     * @param $files
     * @param $cookie
     * @param $header
     * @param $serverInfo
     * @param SessionConfig|null $sessionConfig
     * @param string|null $sessionId
     */
    public function __construct(
        $get,
        $post,
        $files,
        $cookie,
        $header,
        $serverInfo,
        ?SessionConfig $sessionConfig,
        ?string $sessionId
    )
    {
        $this->get = is_null($get) ? [] : $get;
        $this->post = is_null($post) ? [] : $post;
        $this->files = is_null($files) ? [] : $files;
        $this->cookie = is_null($cookie) ? [] : $cookie;
        $this->header = is_null($header) ? [] : $header;
        $this->serverInfo = is_null($serverInfo) ? [] : $serverInfo;
        $this->sessionConfig = $sessionConfig;
        $this->sessionId = $sessionId;
    }

    /**
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @return SessionConfig|null
     */
    public function getSessionConfig()
    {
        return $this->sessionConfig;
    }

    /**
     * raw content
     * @return string
     */
    abstract public function rawContent(): string;

    /**
     * Get GET parameters
     * @param $name
     * @return mixed|array|string|int|null
     */
    public function get($name = null)
    {
        if (is_null($name)) return $this->get;
        $value = Build::getInstance()->getData($this->get, $name);
        XSS::getInstance()->filter($value);
        return $value;
    }

    /**
     * Get POST parameters
     * @param $name
     * @return mixed|array|string|int|null
     */
    public function post($name = null)
    {
        if (is_null($name)) return $this->post;
        $value = Build::getInstance()->getData($this->post, $name);
        XSS::getInstance()->filter($value);
        return $value;
    }

    /**
     * Get COOKIE parameters
     * @param $name
     * @return mixed|array|string|int|null
     */
    public function cookie($name = null)
    {
        if (is_null($name)) return $this->cookie;
        $value = Build::getInstance()->getData($this->cookie, $name);
        XSS::getInstance()->filter($value);
        return $value;
    }

    /**
     * get session params
     * @param $name
     * @return mixed|array|string|int|null
     * @throws Exception
     */
    public function session($name = null)
    {
        $sessionConfig = $this->sessionConfig;

        if ($sessionConfig == null) return $name ? null : [];

        if (empty($this->sessionId)) return $name ? null : [];

        $cacheService = $sessionConfig->getService();

        if ($cacheService == null) return $name ? null : [];

        $cacheKey = 'session_' . $this->getSessionId();

        $cacheData = $cacheService->get($cacheKey);

        if (!is_array($cacheData)) return $name ? null : [];

        XSS::getInstance()->filter($cacheData);

        foreach ($cacheData as $index => $datum) {
            if (is_string($datum)) $datum = unserialize($datum);

            if (!is_null($name) && $name == $index) return $datum;

            $cacheData[$index] = $datum;
        }

        return $cacheData;
    }

    /**
     * get file
     * @param $name
     * @return mixed|array|string|int|null
     */
    public function file($name = null)
    {
        if (is_null($name)) return $this->files;

        $value = Build::getInstance()->getData($this->files, $name);

        XSS::getInstance()->filter($value);

        return $value;
    }

    /**
     * request(get|post|cookie|session|put,file)
     * @param $name
     * @return array|null|string
     * @throws Exception
     */
    public function request($name = null)
    {
        $req = array_merge($this->cookie(), $this->session(), $this->file(), $this->get(), $this->post());

        if (is_null($name)) return $req;

        $value = Build::getInstance()->getData($req, $name);

        XSS::getInstance()->filter($value);

        return $value;
    }

    /**
     * Get request changes
     * @param $name
     * @param $method :方法(get|post|cookie|session|put)
     * @return mixed|array|string|int|null
     * @throws Exception
     */
    public function getParam($name, $method = null)
    {
        switch (strtoupper($method)) {
            case HttpConfig::PARAM_GET:
                return $this->get($name);
            case HttpConfig::PARAM_POST:
                return $this->post($name);
            case HttpConfig::PARAM_COOKIE:
                return $this->cookie($name);
            case HttpConfig::PARAM_PUT:
                throw new Exception('Put request not supported');
            case HttpConfig::PARAM_SESSION:
                return $this->session($name);
            case HttpConfig::PARAM_FILE:
                return $this->file($name);
            default:
                return $this->request($name);
        }
    }


    /**
     * GET header
     * @param $name
     * @return mixed|array|string|int|null
     */
    public function header($name = null)
    {
        if (is_null($name)) return $this->header;

        $value = Build::getInstance()->getData($this->header, $name);

        XSS::getInstance()->filter($value);

        return $value;
    }

    /**
     * @return array
     */
    public function serverInfo(): array
    {
        return $this->serverInfo;
    }

    /**
     * Get Request scheme
     * @return mixed|string
     */
    public function getScheme(): string
    {
        $mode = Build::getInstance()->getData($this->serverInfo, 'REQUEST_SCHEME');

        return $mode ? $mode : 'http';
    }

    /**
     * Get current visiting Url
     * @param bool|false $meter
     * @param bool $isDecode
     * @return string
     */
    public function getUrl(bool $meter = false, bool $isDecode = true): string
    {
        $mode = $this->getScheme();

        $host = Build::getInstance()->getData($this->header, 'Host');

        $request = Build::getInstance()->getData($this->serverInfo, 'REQUEST_URI');

        $url = $mode . '://' . $host . $request;

        if ($meter === false && is_int(strpos($url, '?'))) {

            $url = Build::getInstance()->getData(explode('?', $url), 0);
        }

        return $isDecode ? urldecode($url) : $url;
    }

    /**
     * Get client side Ip
     * @return mixed
     */
    public function getIp(): ?string
    {
        if (isset($this->header['X-Real-Ip'])) {
            $ip = $this->header['X-Real-Ip'];
        } else if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = Build::getInstance()->getData($this->serverInfo, 'REMOTE_ADDR');

            if (empty($ip)) $ip = Build::getInstance()->getData($this->serverInfo, 'REMOTE_IP');
        }

        XSS::getInstance()->filter($ip);

        return $ip;
    }

    /**
     * Get user Agent
     * @return array|null|string
     */
    public function getUserAgent(): ?string
    {
        return $this->header('User-Agent');
    }

    /**
     * Get host root url
     * @param string $rootPath
     * @return mixed
     */
    public function getHostUrl(string $rootPath = PATH_PUBLIC): string
    {
        $mode = $this->getScheme();

        $host = Build::getInstance()->getData($this->header, 'Host');

        XSS::getInstance()->filter($host);

        $root = Build::getInstance()->getData($this->serverInfo, 'DOCUMENT_ROOT');

        XSS::getInstance()->filter($root);

        if (empty($root)) $root = $rootPath;

        $rootDir = str_replace($root, '', $rootPath);

        $rootDir = substr($rootDir, 0, 1) != '/' ? "/$rootDir" : $rootDir;

        return $mode . "://{$host}{$rootDir}";
    }

    /**
     * 获取请求的文档路径
     * @return string
     */
    public function getDocumentRoot(): string
    {
        $hostUrl = $this->getHostUrl();

        $url = $this->getUrl();

        $documentRoot = (string)str_replace($hostUrl, '', $url);

        if (empty($documentRoot)) $documentRoot = '/';

        return $documentRoot;
    }

    /**
     * 获取请求方法
     * @return array|null|string
     */
    public function getMethod(): ?string
    {
        $value = Build::getInstance()->getData($this->serverInfo, 'REQUEST_METHOD');

        XSS::getInstance()->filter($value);

        return $value;
    }

    /**
     * 通过参数生成url
     * @param $path
     * @return string
     */
    public function toUrl($path = null): string
    {
        $args = func_get_args();

        if (!is_string($path)) {
            $path = $this->getUrl();
        } else {
            if (isset($args[0]) && $path === $args[0]) unset($args[0]);

            $path = $this->getHostUrl() . $path;
        }

        $data = [];

        foreach ($args as $value) {
            if ($value instanceof Model) {
                try {
                    $data = array_merge($data, $value->toData());
                } catch (Exception $E) {

                }
            } else {
                $data = array_merge($data, (array)$value);
            }
        }

        foreach ($data as $name => $value) {
            if (is_array($value) || is_object($value)) {
                $data[$name] = json_encode($value);
            }
        }

        $pathParam = Build::getInstance()->getRegularAll('/\{(\w+)\}/i', $path);

        $replace = [];

        foreach ($data as $name => $value) {
            $key = is_numeric($name) ? Build::getInstance()->getData($pathParam, $name) : $name;

            if (is_int(strpos($path, "{{$key}}"))) {

                unset($data[$name]);

                $replace["{{$key}}"] = $value;
            }
        }

        $path = str_replace(array_keys($replace), $replace, $path);

        $query = Uri::getInstance()->toQuery($data);

        return $path . (!empty($query) ? '?' . $query : '');
    }

    /**
     * 判断是否微信客户端
     * @return bool
     */
    public function isWXClient(): bool
    {
        return is_int(strpos($this->getUserAgent(), 'MicroMessenger'));
    }

    /**
     * 判断是否移动端
     * @return bool
     */
    public function isMobile(): bool
    {
        $userAgent = $this->getUserAgent();

        if (is_int(strpos($userAgent, 'iphone')))
            return true;

        return is_int(strpos($userAgent, 'android'));
    }
}
