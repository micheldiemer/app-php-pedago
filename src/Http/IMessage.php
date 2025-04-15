<?php

namespace App\Http;

interface IMessage
{
    public function get(string $header): ?string;
    public function getAll(): array;
    public function setHeader(string $header, string|array|null $_value): Message;
    public function removeHeader(string $header): Message;

    public function getBody(): string;
    public function withBody(string|\Stringable $data): Message;
    public function appendBody(string|\Stringable $data): Message;
}
