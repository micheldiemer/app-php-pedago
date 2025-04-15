<?php

namespace App\App;

use App\Logger\ILogger;
use App\Logger\FileLogger;

use App\Http\Request;
use App\Http\Response;



use Symfony\Component\Dotenv\Dotenv;




class App
{
    private $_name;
    private $_app_root;
    private $dotenv;
    private ILogger $_logger;
    private Request $_request;
    private Response $_reponse;

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
        $this->_request = new Request($this->_logger);
        $this->_reponse = new Response($this->_logger);
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

    public function req(): Request
    {
        return $this->request();
    }

    public function request(): Request
    {
        return $this->_request;
    }

    public function rep(): Response
    {
        return $this->reponse();
    }

    public function reponse(): Response
    {
        return $this->_reponse;
    }

    public function name(): string
    {
        return $this->_name;
    }
}
