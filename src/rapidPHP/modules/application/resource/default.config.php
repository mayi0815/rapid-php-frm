<?php

return [
    'application' => [
        'scans' => [
            'controller' => [
                '${PATH_APP}classier/controller/',
            ],
        ],

        'web' => [
            'view' => [
                'ext' => ['php', 'html', 'htm'],
                'template_path' => '${PATH_APP}classier/view/',
                'cache_path' => '${PATH_APP}runtime/build/view/',
                'template_service' => \rapidPHP\modules\core\classier\web\template\TemplateService::class,
            ],
        ],
    ],
    'log' => [
        'error' => [
            'name' => 'error',
            'size' => 1024 * 1024 * 5,
            'path' => '${PATH_APP_RUNTIME}log/error/{number}.log',
        ],
        'warning' => [
            'name' => 'warning',
            'size' => 1024 * 1024 * 5,
            'path' => '${PATH_APP_RUNTIME}log/warning/{number}.log',
        ],
        'debug' => [
            'name' => 'debug',
            'size' => 1024 * 1024 * 5,
            'path' => '${PATH_APP_RUNTIME}log/debug/{number}.log',
        ],
        'access' => [
            'name' => 'access',
            'size' => 1024 * 1024 * 5,
            'path' => '${PATH_APP_RUNTIME}log/access/{number}.log',
        ],
    ],
    'console'=>[
        'session' => [
            'key' => 'PHPSESSID',

            'service' => null,
        ],

        'context' => \rapidPHP\modules\application\classier\context\ConsoleContext::class,
    ],
    'server' => [
        'cgi' => [
            'session' => [
                'key' => 'PHPSESSID',

                'service' => null,
            ],

            'context' => \rapidPHP\modules\application\classier\context\WebContext::class,
        ],
        'swoole' => [
            'session' => [
                'key' => 'PHPSESSID',

                'service' => null,
            ],

            'context' => \rapidPHP\modules\application\classier\context\WebContext::class,

            'co' => [
                'hook_flags' => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL
            ],

            'options' => [
                /**
                 * Enable static file processing
                 */
                'enable_static_handler' => true,

                /**
                 * Static file directory
                 */
                'document_root' => '${PATH_APP}public/',

                /*
                 * Set the asynchronous restart switch. When set to true, the asynchronous safe restart feature will be enabled, and the Worker process will wait for asynchronous events to complete before exiting.
                 */
                'reload_async' => true,

                /**
                 * The enable_coroutine option is equivalent to turning off the SW_COROUTINE macro switch in the callback of the previous version, and when turned off, no coroutine is created in the callback event, but the ability to create a coroutine by the user is retained.
                 */
                'enable_coroutine' => true,

                /**
                 * Set the maximum data packet size in bytes.
                 * After opening the protocol parsing of open_length_check/open_eof_check/open_http_protocol, the underlying layer will perform data packet splicing.
                 * At this time, when the data packet is not received completely, all data is saved in memory.
                 */
                'package_max_length' => 50 * 1024 * 1024,

                /**
                 * Set the number of Worker processes started.
                 * The business code is fully asynchronous and non-blocking, and it is most reasonable to set it to 1-4 times the number of CPU cores here.
                 * If the business code is synchronous and blocking, it needs to be adjusted according to the request response time and system load, for example: 100-500.
                 * The default is set to SWOOLE_CPU_NUM, and the maximum cannot exceed SWOOLE_CPU_NUM * 1000.
                 * For example, if a request takes 100ms and a processing capacity of 1000QPS is required, 100 processes or more must be configured. However, the more processes are opened, the more memory will be occupied, and the overhead of process switching will become larger and larger. So it is appropriate here. Don't configure it too large.
                 * Assuming that each process occupies 40M of memory, 100 processes will occupy 4G of memory.
                 * @var int
                 */
                'worker_num' => 1,

                /**
                 * Configure the number of Task processes. After configuring this parameter, the task function will be enabled. Therefore, the Server must register two event callback functions, onTask and onFinish, otherwise the server program will not start.
                 * The Task process is synchronous and blocking, and the configuration method is the same as the Worker synchronous mode.
                 * The maximum value cannot exceed SWOOLE_CPU_NUM * 1000.
                 * The processing time of a single task, such as 100ms, one process can process 1/0.1=10 tasks per second.
                 * The speed of task delivery, such as producing 2000 tasks per second
                 * 2000/10=200, you need to set task_worker_num => 200, and enable 200 task processes
                 * @var int
                 */
                'task_worker_num' => 10,

            ],

            'http' => [
                'ip' => '0.0.0.0',

                'port' => 1700,

                'options' => [

                    /**
                     * ssl key certificate file
                     */
                    'ssl_key_file' => null,

                    /**
                     * ssl cert certificate file
                     */
                    'ssl_cert_file' => null,
                ]
            ],

            'websocket' => [

                'ip' => '0.0.0.0',

                'port' => 1701,

                'return_key' => '__c', // Define websocket callback block

                'options' => [

                    /**
                     * ssl key certificate file
                     */
                    'ssl_key_file' => null,

                    /**
                     * ssl cert certificate file
                     */
                    'ssl_cert_file' => null,
                    /**
                     * Heartbeat detection Traverse all connections every few seconds
                     */
                    'heartbeat_check_interval' => 30,

                    /**
                     * Heartbeat detection Maximum idle time, trigger close and close
                     * The default is twice the heartbeat_check_interval,
                     * Twice is a fault-tolerant mechanism, and a little more is to make up for network latency
                     */
                    'heartbeat_idle_time' => 30,
                ],
            ],
        ],
    ],

    'redis' => [
        'master' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'select' => 0,
        ],
        'salve' => null
    ],
    'database' => [
        'sql' => [
            'master' => [
                'url' => '',
                'username' => '',
                'password' => '',
            ],
            'salve' => null,
        ],
        'nosql' => null
    ]
];