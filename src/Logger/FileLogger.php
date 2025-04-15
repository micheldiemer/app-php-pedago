<?php

namespace App\Logger;

use App\Logger\ILogger;
use Exception;

class FileLogger implements ILogger
{

    private string $_fileName;
    private $ms;
    private $strms;
    private $pid;
    private $uid;


    public function __construct($filename, $reset = false)
    {
        $this->_fileName = $filename;
        $this->ms = microtime(true);
        $now = \DateTime::createFromFormat('U.u', $this->ms);
        // $this->strms = sprintf("%.23s", $now->format("m-d-Y H:i:s.u"));
        $this->strms = sprintf("%.9s", $now->format("i:s.u"));
        $this->pid = getmypid();
        $this->uid = bin2hex(random_bytes(16));
        $this->checkfile($reset);
    }

    private function checkfile(bool $reset = false)
    {
        if (!file_exists($this->_fileName) || $reset) {
            file_put_contents($this->_fileName, '');
        }
        if (!is_readable($this->_fileName)) {
            throw new \Exception($this->_fileName . ' illisible');
        }
        if (!is_writeable($this->_fileName)) {
            throw new \Exception($this->_fileName . ' illisible');
        }
        if (filesize($this->_fileName) > 10 * 2 ** 10) {
            rename($this->_fileName, $this->_fileName . '.old');
        }
    }

    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            } else {
                $replace['{' . $key . '}'] = var_export($val, true);
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }


    protected function prefix(): string
    {
        $ms = microtime(true);
        $now = \DateTime::createFromFormat('U.u', $ms);
        $strms = $now->format("s.u");
        return sprintf("%s:%.5s:%.6s", $this->strms, $this->pid, $strms);
    }

    public function log(string $level, string|\Stringable $message, array $context = [])
    {
        $interpolated = $this->interpolate($message, $context);
        $prefix = $this->prefix();

        $line = PHP_EOL . sprintf("%s:%s", $prefix, $interpolated);

        file_put_contents($this->_fileName, $line, FILE_APPEND) . PHP_EOL;
    }
}
