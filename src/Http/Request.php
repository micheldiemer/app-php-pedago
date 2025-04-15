<?php

namespace App\Http;

use InvalidArgumentException;

class Request extends Message implements IRequest
{
    const HTTP_METHODS = ['GET', 'PUT', 'POST', 'DELETE', 'PATCH', 'OPTIONS'];

    private ?Url $_uri = null;

    public function uri(): Url
    {
        return $this->_uri;
    }


    public string $method {
        get {
            return $this->method;
        }
        set(string $value) {
            $v = trim(strtoupper($value));
            if (!in_array($value, self::HTTP_METHODS)) {
                throw new InvalidArgumentException("$value méthode incorrect");
            }
            $this->method = $v;
        }
    }

    public function __construct($logger, $maxBodyRead = 2 ** 10)
    {
        parent::__construct($logger);

        $this->method = $_SERVER['REQUEST_METHOD'];
        $m = $this->method;

        /*
        function url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}
    */
        $this->_uri = new Url($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);

        $this->_uri->scheme =
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

        $this->_uri->port = $_SERVER['SERVER_PORT'] ?? null;


        $headers = getallheaders();
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        if ($m != 'GET' && $m != 'OPTIONS' && $this->size > 0) {
            $handle = fopen('php://input', 'r');
            $this->withBody(fread($handle, $maxBodyRead));
        }
        $this->log('rq ' . $this->_uri . ' ' . $this->getShortBody());
        $this->log('rq ' . $this->method . ' ' . $this->getShortBody());
    }
}
