<?php
// require_once __DIR__ . '../../../vendor/autoload.php';

namespace App\App;

use App\Logger\ILogger;
use \Logger\FileLogger;


class App
{
    private $_name;
    private $_app_root;
    private ILogger $_logger;


    public function __construct($name, $appRoot)
    {
        die('__construct');
        $this->_name = $name;
        $this->_app_root = $appRoot;
        $logFile = $appRoot . '/logs' . '/app.log';
        $this->setLogger(new FileLogger($logFile));
        $this->log('I', 'app started');
    }

    public function __destruct()
    {
        $this->log('I', 'app finished');
    }

    public function log(string $level, string|\Stringable $message, $context = [])
    {
        $this->_logger->log($level, $message, $context);
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    public function appRoot()
    {
        return $this->_app_root;
    }

    public function name(): string
    {
        return $this->_name;
    }
}
