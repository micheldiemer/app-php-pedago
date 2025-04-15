<?php

namespace App\Http;

class Response extends Message implements IResponse
{
    public int $code {
        get {
            return http_response_code();
        }
        set(int $value) {
            if ($value < 100 || $value >= 600) {
                throw new \InvalidArgumentException("$value code incorrect");
            }
            if (headers_sent() || http_response_code($code) === false) {
                $this->err("header envoyé, impossible de définir le code");
            }
            $this->log("$code code retour");
        }
    }

    private function rep(?int $code, ?string $contentType, string|\Stringable $body, $send = false): Response
    {
        if (is_int($code)) $this->code = $code;
        if (is_string($contentType)) $this->contentType = $contentType;
        $this->withBody($body);
        if ($send) $this->send();
        return $this;
    }

    public function json($data, ?int $code = null): Response
    {
        return $this->rep($code, 'application/json', json_encode($data));
    }

    public function html($data, ?int $code = null): Response
    {
        return $this->rep($code, 'text/html; charset=utf-8', $data);
    }

    public function raw($data, ?int $code = null): Response
    {
        return $this->rep($code, 'text/plain; charset=utf-8', $data);
    }

    public function sendFile($fileName, $attachment = false, ?int $code = null): Response
    {
        if (is_int($code)) $this->code = $code;
        if (!is_readable($fileName)) {
            throw new \Exception("$fileName illisible");
        }
        $disposition = $attachment === false ? 'inline' : "attachment; filename = \"$fileName\"";
        $this->setHeader(
            'Content-Disposition',
            $disposition
        );
        $this->contentType = mime_content_type($fileName);
        $this->setHeader('Content-Length', filesize($fileName));

        $this->log(sprintf('réponse %s %s', $this->code, $this->contentType));
        $this->log("envoi fichier $fileName");

        $h = fopen($fileName, 'r');
        $data = '';
        while (!feof($h) && $data !== false) {
            $data = fread($h, 4 * 1024);
            if ($data !== false) {
                echo $data;
            }
        }
        return $this;
    }


    public function send(?int $code = null): Response
    {
        if (is_int($code)) $this->code = $code;
        $this->log(sprintf('réponse %s %s', $this->code, $this->contentType));
        $this->log(sprintf("%.120s", $this->getShortBody()));
        echo $this->getBody();
        return $this;
    }
}
