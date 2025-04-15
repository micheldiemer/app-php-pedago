<?php

namespace App\Http;

use Exception;

interface IResponse
{
    public function json($data): IResponse;
    public function html($data): IResponse;
    public function raw($data): IResponse;


    public function sendFile(string $fileName, false|string $sendName);

    public function send();
}
