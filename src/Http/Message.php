<?php

namespace App\Http;

class Message implements IMessage
{
    /**
     * http headers
     *
     * @var array
     */
    private array $headers = [];

    private string $body = '';

    private $logger;

    public function __construct($logger)
    {
        $this->setLogger($logger);
    }

    public ?string $contentType {
        get {
            return $this->headers['Content-Type'] ?? null;
        }
        set(?string $value) {
            $this->setHeader('Content-Type', $value);
        }
    }

    public function get($header): ?string
    {
        return $this->headers[trim($header)] ?? null;
    }



    public function getAll(): array
    {
        return $this->headers;
    }

    public function log($message)
    {
        $this->logger->log('D', __NAMESPACE__ . '/' . static::class . ' ' . $message);
    }

    public function err($message)
    {
        $this->logger->log('E', __NAMESPACE__ . '/' . __CLASS__ . ' ' . $message);
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function setHeader(string $header, string|array|null $_value): Message
    {
        $hd = trim($header);
        if (empty($_value)) {
            return $this->removeHeader($hd);
        }
        $v = '';
        if (is_array($_value)) {
            $v = trim($_value[0]);
            for ($i = 1; $i < count($_value); $i++) {
                $v .= ',';
                trim($v);
            }
        } else {
            $v = trim($_value);
        }

        $headers[$hd] = $v;
        $p = "$hd: $v";
        $this->log(__FUNCTION__, $p);

        if (headers_sent()) {
            $this->err(__FUNCTION__, 'headers_sent / not defined');
            return $this;
        }
        header($p);
        return $this;
    }

    public function removeHeader(string $header): Message
    {
        $hd = trim($header);
        unset($headers[$hd]);
        $this->log(__FUNCTION__, "$hd removed");
        if (headers_sent()) {
            $this->err(__FUNCTION__, 'headers_sent / not defined');
            return $this;
        }
        header_remove($hd);
        return $this;
    }

    public function getShortBody(): string
    {
        $first255 = mb_trim(mb_substr($this->body, 0, 255));
        return preg_replace('/\r\n+|\r+|\n+/', ' ', $first255);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function withBody(string|\Stringable $data): Message
    {
        $this->body = strval($data);
        return $this;
    }

    public function appendBody(string|\Stringable $data): Message
    {
        $this->body .= strval($data);
        return $this;
    }
}
