<?php

namespace App\Http;

class Response extends Message implements IResponse
{
    public function json($data): Response
    {
        $this->contentType = 'application/json';
        $this->withBody(json_encode($data));
        return $this;
    }

    public function html($data): Response
    {
        $this->contentType = 'text/html; charset=utf-8';
        $this->withBody($data);
        return $this;
    }

    public function raw($data): Response
    {
        $this->contentType = 'text/plain; charset=utf-8';
        $this->withBody($data);
        return $this;
    }

    public function sendFile($fileName, $attachment)
    {
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

        $h = fopen($fileName, 'r');
        $data = '';
        while (!feof($h) && $data !== false) {
            $data = fread($h, 4 * 1024);
            if ($data !== false) {
                echo $data;
            }
        }
    }


    public function send()
    {
        $this->log('envoi de la réponse http ' . $this->contentType);
        $this->log(sprintf("%.120s", $this->getShortBody()));
        echo $this->getBody();
    }
}
