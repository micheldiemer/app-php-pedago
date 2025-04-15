<?php

namespace App\Http;

interface IResponse
{
    public int $code { get; set; }
    public function json($data, ?int $code = null): IResponse;
    public function html($data, ?int $code = null): IResponse;
    public function raw($data, ?int $code = null): IResponse;
    public function sendFile(
        string $fileName,
        false|string $sendName = false,
        ?int $code = null
    ): IResponse;
    public function send(?int $code = null): IResponse;
}
