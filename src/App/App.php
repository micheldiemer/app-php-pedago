<?php

namespace App\App;

use App\Logger\ILogger;
use App\Logger\FileLogger;

use Symfony\Component\Dotenv\Dotenv;




class App
{
    private $_name;
    private $_app_root;
    private $dotenv;
    private ILogger $_logger;

    public function loadEnv($file)
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->loadEnv($file);
    }

    public function __construct($name, $appRoot, $envFile = '.env')
    {
        $this->_name = $name;
        $this->_app_root = $appRoot;
        $logFile = $appRoot . '/logs' . '/app.log';
        $this->setLogger(new FileLogger($logFile));
        $this->loadEnv($appRoot . '/' . $envFile);
        $this->log('I', 'app ' . $_ENV['APP_NAME'] . ' started');
    }

    public function __destruct()
    {
        $this->log('I', 'app ' . $_ENV['APP_NAME'] . ' finished');
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
